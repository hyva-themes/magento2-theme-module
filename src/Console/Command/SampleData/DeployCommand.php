<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Console\Command\SampleData;

use Composer\Console\Application as ComposerApplication;
use Composer\Repository\ComposerRepository;
use Composer\Repository\PathRepository;
use Composer\Repository\RepositoryInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Composer\ComposerFactory;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Deploy Hyvä sample data packages based on installed Magento modules.
 *
 * Discovers which Luma sample data packages are suggested by installed modules,
 * maps them to Hyvä equivalents via a naming convention, and runs composer require.
 */
class DeployCommand extends Command
{
    public const OPTION_NO_UPDATE = "no-update";
    public const OPTION_REPLACE_LUMA = "replace-luma";
    public const OPTION_REINSTALL = "reinstall";
    public const OPTION_KEEP_LUMA = "keep-luma";

    private const HYVA_SAMPLE_DATA_PACKAGE_PREFIX = "hyva-themes/koti-sample-data-";
    private const HYVA_SAMPLE_DATA_SUGGEST = "Hyvä Sample Data version:";
    private const RESET_FLAG_FILE = ".hyva-sample-data-reset";
    private const KEEP_LUMA_CONFIG_FILE = "hyva-sample-data-config.json";

    /**
     * The core sample data dependency resolver.
     *
     * Not constructor-injected so the class is not required at DI-compilation
     * time. Customers sometimes composer-replace magento/module-sample-data as
     * a performance optimisation, which removes this class; a hard dependency
     * would make setup:di:compile fail for them. The class is resolved lazily
     * via the object manager and guarded with class_exists() at runtime.
     */
    private const SAMPLE_DATA_DEPENDENCY_CLASS = \Magento\SampleData\Model\Dependency::class;

    /** Packages that don't follow the magento/module-{X}-sample-data convention. */
    private const EXPLICIT_PACKAGE_MAP = [
        "magento/sample-data-media" =>
            self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX . "media",
    ];

    /**
     * Config paths written by Luma sample-data installers.
     *
     * Only full-value writes are listed — paths that are appended to (e.g.
     * design/head/includes in module-theme-sample-data) cannot be reliably
     * reverted without snapshotting the pre-install value, so they are left
     * alone for the operator to clean up manually if needed.
     */
    private const LUMA_CONFIG_PATHS = [
        "magento/module-msrp-sample-data" => ["sales/msrp/enabled"],
        "magento/module-multiple-wishlist-sample-data" => [
            "wishlist/general/multiple_enabled",
        ],
        "magento/module-offline-shipping-sample-data" => [
            "carriers/tablerate/active",
            "carriers/tablerate/condition_name",
        ],
    ];

    private Filesystem $filesystem;
    private ObjectManagerInterface $objectManager;
    private ComposerInformation $composerInformation;
    private ComposerFactory $composerFactory;
    private ResourceConnection $resourceConnection;
    private CacheManager $cacheManager;

    public function __construct(
        Filesystem $filesystem,
        ObjectManagerInterface $objectManager,
        ComposerInformation $composerInformation,
        ComposerFactory $composerFactory,
        ResourceConnection $resourceConnection,
        CacheManager $cacheManager,
    ) {
        $this->filesystem = $filesystem;
        $this->objectManager = $objectManager;
        $this->composerInformation = $composerInformation;
        $this->composerFactory = $composerFactory;
        $this->resourceConnection = $resourceConnection;
        $this->cacheManager = $cacheManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName("hyva:sampledata:deploy")->setDescription(
            "Deploy Hyvä sample data packages (replaces Luma sample data)",
        );
        $this->addOption(
            self::OPTION_NO_UPDATE,
            null,
            InputOption::VALUE_NONE,
            "Update composer.json without executing composer update",
        );
        $this->addOption(
            self::OPTION_REPLACE_LUMA,
            null,
            InputOption::VALUE_NONE,
            "Remove all Luma sample data modules and clear existing data (DESTRUCTIVE: removes all products, orders and customers)",
        );
        $this->addOption(
            self::OPTION_REINSTALL,
            null,
            InputOption::VALUE_NONE,
            "Reset sample data so next setup:upgrade recreates all catalog data (DESTRUCTIVE: removes all products, orders and customers)",
        );
        $this->addOption(
            self::OPTION_KEEP_LUMA,
            null,
            InputOption::VALUE_NONE,
            "Install Koti sample data on a separate website, leaving the default website untouched",
        );
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->updateMemoryLimit();

        // Guard before any side effects: the destructive --replace-luma /
        // --reinstall paths run before the sample data resolver is used, and
        // Luma removal is not rolled back. If the core package has been
        // composer-replaced the resolver class is absent — fail cleanly here
        // rather than half-way through.
        if (!class_exists(self::SAMPLE_DATA_DEPENDENCY_CLASS)) {
            $output->writeln(sprintf(
                "<error>The required package magento/module-sample-data is not installed " .
                    "(the %s class could not be found). " .
                    "Install magento/module-sample-data to use hyva:sampledata:deploy.</error>",
                self::SAMPLE_DATA_DEPENDENCY_CLASS,
            ));
            return Cli::RETURN_FAILURE;
        }

        $baseDir = $this->filesystem
            ->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath();
        $replaceLuma = $input->getOption(self::OPTION_REPLACE_LUMA);
        $keepLuma = $input->getOption(self::OPTION_KEEP_LUMA);
        $reinstall = $input->getOption(self::OPTION_REINSTALL);
        $noUpdate = $input->getOption(self::OPTION_NO_UPDATE);

        if ($keepLuma && $replaceLuma) {
            $output->writeln(
                "<error>--keep-luma and --replace-luma are mutually exclusive.</error>",
            );
            return Cli::RETURN_FAILURE;
        }

        if ($keepLuma && !$reinstall && $this->isSampleDataInstalled()) {
            $output->writeln(
                "<error>Sample data already installed. Use --reinstall --keep-luma to migrate to a separate website.</error>",
            );
            return Cli::RETURN_FAILURE;
        }

        if (!$keepLuma && !$replaceLuma && $this->isLumaSampleDataInstalled()) {
            $output->writeln(
                "<error>Luma sample data is installed. Use --keep-luma to install Koti on a separate website, or --replace-luma to remove Luma first.</error>",
            );
            return Cli::RETURN_FAILURE;
        }

        // Rollback stack: each side effect registers its undo action. On any
        // failure path after the first side effect, cleanups are run in reverse
        // order so the filesystem and DB are restored to their pre-command state.
        // Luma removal is intentionally NOT registered — it is a commit point.
        $cleanups = [];
        $success = false;

        try {
            if ($replaceLuma) {
                $result = $this->removeLumaSampleData($baseDir, $noUpdate, $output);
                if ($result !== Cli::RETURN_SUCCESS) {
                    $success = true; // nothing registered yet
                    return $result;
                }
            }

            if ($replaceLuma || $reinstall) {
                $this->prepareReinstall($output, $cleanups);
            }

            if ($keepLuma) {
                $this->writeKeepLumaConfig($output, $cleanups);
            }

            // Resolved lazily (not constructor-injected) so this class carries
            // no compile-time dependency on magento/module-sample-data. The
            // class_exists() guard at the top of execute() guarantees it loads.
            /** @var \Magento\SampleData\Model\Dependency $sampleDataDependency */
            $sampleDataDependency = $this->objectManager->get(self::SAMPLE_DATA_DEPENDENCY_CLASS);
            $magentoPackages = $sampleDataDependency->getSampleDataPackages();

            // Also discover Hyvä-specific suggestions from composer.lock.
            // Hyvä modules use "Hyvä Sample Data version:" as prefix so the core
            // sampledata:deploy command (which only knows "Sample Data version:") ignores them.
            // Only reads composer.lock; the core getSuggestsFromModules() on-disk scan is not
            // used because it only walks one parent dir up from each registered module path,
            // which doesn't reach the package-level composer.json for multi-module packages.
            foreach ($this->composerInformation->getSuggestedPackages() as $name => $version) {
                if ($version !== null && str_starts_with($version, self::HYVA_SAMPLE_DATA_SUGGEST)) {
                    $magentoPackages[$name] = trim(substr($version, strlen(self::HYVA_SAMPLE_DATA_SUGGEST)));
                }
            }

            if (empty($magentoPackages)) {
                $output->writeln(
                    "<info>No sample data suggestions found for installed modules.</info>",
                );
                return Cli::RETURN_FAILURE;
            }

            $hyvaPackages = [];
            $unmapped = [];
            foreach ($magentoPackages as $name => $version) {
                if (str_starts_with($name, self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX)) {
                    $hyvaPackages[$name] = "*";
                } elseif ($hyvaName = $this->mapToHyvaPackage($name)) {
                    $hyvaPackages[$hyvaName] = "*";
                } else {
                    $unmapped[] = $name;
                }
            }

            if (empty($hyvaPackages)) {
                $output->writeln(
                    "<info>No Hyvä sample data packages available for the installed modules.</info>",
                );
                return Cli::RETURN_FAILURE;
            }

            [$available, $availabilityConclusive] = $this->getAvailableHyvaPackages($output);
            $unavailable = [];
            if ($availabilityConclusive) {
                // We have an authoritative list of what exists, so drop anything that
                // doesn't (e.g. a package derived by naming convention that has no real
                // Hyvä equivalent) before requiring.
                $unavailable = array_diff_key($hyvaPackages, $available);
                $hyvaPackages = array_intersect_key($hyvaPackages, $available);
            }

            if (empty($hyvaPackages)) {
                $output->writeln(
                    "<info>No Hyvä sample data packages are available in Composer repositories.</info>",
                );
                return Cli::RETURN_FAILURE;
            }

            $output->writeln("<info>Requiring Hyvä sample data packages:</info>");
            foreach ($hyvaPackages as $name => $version) {
                $output->writeln("  - $name:$version");
            }
            if (!empty($unmapped)) {
                $output->writeln("");
                $output->writeln("<comment>No Hyvä equivalent for:</comment>");
                foreach ($unmapped as $name) {
                    $output->writeln("  - $name");
                }
            }
            if (!empty($unavailable)) {
                $output->writeln("");
                $output->writeln(
                    "<comment>Suggested but not available in Composer:</comment>",
                );
                foreach ($unavailable as $name => $version) {
                    $output->writeln("  - $name");
                }
            }
            $output->writeln("");

            $this->removeStaleHyvaPackages($hyvaPackages, $baseDir, $output);

            $packages = [];
            foreach ($hyvaPackages as $name => $version) {
                $packages[] = "$name:$version";
            }

            if ($availabilityConclusive || $noUpdate) {
                // Single atomic require. Either every package is known to exist
                // (conclusive), or --no-update was requested: with --no-update Composer
                // only edits composer.json and performs no resolution, so it can neither
                // verify a package exists nor fail on a missing one. The per-package
                // fallback below would gain nothing there and would falsely report every
                // spec as installed, so we defer that verification to the later update.
                if ($noUpdate && !$availabilityConclusive) {
                    $output->writeln(
                        "<comment>Availability could not be verified and --no-update skips " .
                            "dependency resolution, so any package that does not exist will " .
                            "only surface on the next 'composer update' / setup:upgrade.</comment>",
                    );
                    $output->writeln("");
                }
                $result = $this->runComposerRequire($packages, $baseDir, $noUpdate, $output);
                if ($result !== 0) {
                    $output->writeln(
                        "<error>Error during Hyvä sample data deployment. Composer changes will be reverted.</error>",
                    );
                    return Cli::RETURN_FAILURE;
                }
            } else {
                // Availability could not be verified, so the list may contain a package
                // that does not exist. A single atomic require (with update) would abort
                // on it and take the valid packages down with it, so require each package
                // on its own — a missing one is then skipped instead of blocking the rest.
                $installed = [];
                $failed = [];
                foreach ($packages as $spec) {
                    if ($this->runComposerRequire([$spec], $baseDir, $noUpdate, $output) === 0) {
                        $installed[] = $spec;
                    } else {
                        $failed[] = $spec;
                    }
                }

                if (empty($installed)) {
                    $output->writeln(
                        "<error>No Hyvä sample data packages could be installed. Composer changes have been reverted.</error>",
                    );
                    return Cli::RETURN_FAILURE;
                }

                if (!empty($failed)) {
                    $output->writeln("");
                    $output->writeln(
                        "<comment>Skipped packages that could not be installed (they may not exist yet):</comment>",
                    );
                    foreach ($failed as $spec) {
                        $output->writeln("  - $spec");
                    }
                }
            }

            $output->writeln("");
            $output->writeln(
                "<info>Hyvä sample data packages have been added via Composer." .
                    " Please run bin/magento setup:upgrade</info>",
            );

            $success = true;
            return Cli::RETURN_SUCCESS;
        } finally {
            if (!$success) {
                $this->runCleanups($cleanups, $output);
            }
        }
    }

    /**
     * Run registered cleanup callables in reverse order.
     *
     * Each callable is wrapped so one broken cleanup cannot prevent the
     * others from running. Failures are surfaced as a comment and swallowed.
     *
     * @param array<int, callable> $cleanups
     */
    private function runCleanups(array $cleanups, OutputInterface $output): void
    {
        foreach (array_reverse($cleanups) as $undo) {
            try {
                $undo();
            } catch (\Throwable $e) {
                $output->writeln(
                    "<comment>Rollback step failed: {$e->getMessage()}</comment>",
                );
            }
        }
    }

    /**
     * Check whether Koti sample data DataPatches have already been applied.
     */
    private function isSampleDataInstalled(): bool
    {
        $connection = $this->resourceConnection->getConnection();
        $patchListTable = $this->resourceConnection->getTableName("patch_list");
        $count = (int) $connection->fetchOne(
            "SELECT COUNT(*) FROM {$patchListTable} WHERE patch_name LIKE ?",
            ['Hyva\\\\KotiSampleData%'],
        );
        return $count > 0;
    }

    /**
     * Check whether Luma sample data packages are installed via Composer.
     */
    private function isLumaSampleDataInstalled(): bool
    {
        $installedPackages = $this->composerInformation->getInstalledMagentoPackages();
        foreach ($installedPackages as $name => $info) {
            if (preg_match('#^magento/module-.+-sample-data$#', $name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Write the keep-luma config file so setup:upgrade creates a separate website.
     *
     * Registers a cleanup that deletes the file — needed because the file is
     * only meaningful once the matching sample-data packages are required. If
     * composer require fails, the stale config would otherwise survive into
     * the next run and silently steer it onto a separate website.
     *
     * @param array<int, callable> $cleanups
     */
    private function writeKeepLumaConfig(OutputInterface $output, array &$cleanups): void
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $config = [
            "website_code" => "koti_web",
            "store_code" => "koti_store",
            "store_view_code" => "koti",
            "root_category_name" => "Koti",
        ];
        $varDir->writeFile(
            self::KEEP_LUMA_CONFIG_FILE,
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        );
        $cleanups[] = function () use ($varDir): void {
            if ($varDir->isExist(self::KEEP_LUMA_CONFIG_FILE)) {
                $varDir->delete(self::KEEP_LUMA_CONFIG_FILE);
            }
        };
        $output->writeln(
            "<info>Created var/" .
                self::KEEP_LUMA_CONFIG_FILE .
                " — Koti sample data will be installed on a separate website.</info>",
        );
    }

    /**
     * Remove installed Magento Luma sample data packages via Composer.
     */
    private function removeLumaSampleData(
        string $baseDir,
        bool $noUpdate,
        OutputInterface $output,
    ): int {
        $installedPackages = $this->composerInformation->getInstalledMagentoPackages();
        $toRemove = [];
        foreach ($installedPackages as $name => $info) {
            // .+ requires at least one char between module- and -sample-data,
            // so magento/module-sample-data (framework module) cannot match.
            if (preg_match('#^magento/module-.+-sample-data$#', $name)) {
                $toRemove[] = $name;
            }
        }

        if (!empty($toRemove)) {
            $output->writeln(
                "<info>Removing Luma sample data packages:</info>",
            );
            foreach ($toRemove as $name) {
                $output->writeln("  - $name");
            }
            $output->writeln("");

            $arguments = [
                "command" => "remove",
                "packages" => $toRemove,
                "--working-dir" => $baseDir,
                "--no-interaction" => true,
                "--no-progress" => true,
            ];

            if ($noUpdate) {
                $arguments["--no-update"] = true;
            }

            $application = new ComposerApplication();
            $application->setAutoExit(false);
            $result = $application->run(new ArrayInput($arguments), $output);

            if ($result !== 0) {
                $output->writeln(
                    "<error>Error removing Luma sample data packages.</error>",
                );
                return Cli::RETURN_FAILURE;
            }

            $this->resetLumaConfig($toRemove, $output);
            $this->flushCaches($output);

            $output->writeln("");
        } else {
            $output->writeln(
                "<info>No Luma sample data packages found to remove.</info>",
            );
            $output->writeln("");
        }

        return Cli::RETURN_SUCCESS;
    }

    /**
     * Flush all cache backends so the next setup:upgrade does not trip over
     * stale references to removed Luma modules.
     *
     * The trigger symptom is Magento failing DI compilation with
     * "Plugin class Magento\ThemeSampleData\Plugin\View\Page\Config
     * doesn't exist": the `config` cache type had already serialized the
     * plugin definition from module-theme-sample-data, and after removing
     * the module the cached reference points at a class that can no longer
     * be autoloaded. Flushing cache backends forces everything to be
     * rebuilt from the current set of installed modules.
     *
     * Equivalent to running `bin/magento cache:flush` manually.
     */
    private function flushCaches(OutputInterface $output): void
    {
        $types = $this->cacheManager->getAvailableTypes();
        $this->cacheManager->flush($types);
        $output->writeln(
            "<info>Flushed cache backends so next setup:upgrade can rebuild DI.</info>",
        );
    }

    /**
     * Delete core_config_data rows written by Luma sample-data installers.
     *
     * Luma patches register in patch_list once applied, so the DataPatch will
     * not re-run after removal — but the config writes they performed survive
     * and leak into the Hyvä install. The most visible symptom is
     * wishlist/general/multiple_enabled=1 (written by module-multiple-wishlist-
     * sample-data), which makes Hyvä render the multi-wishlist table layout
     * instead of the default list layout.
     *
     * Deletes all scopes of each path — Luma writes at default scope, but
     * deleting every scope is safer if a merchant has since overridden the
     * value per-website.
     *
     * @param string[] $removedPackages Package names that were just removed.
     */
    private function resetLumaConfig(array $removedPackages, OutputInterface $output): void
    {
        $paths = [];
        foreach ($removedPackages as $package) {
            if (isset(self::LUMA_CONFIG_PATHS[$package])) {
                foreach (self::LUMA_CONFIG_PATHS[$package] as $path) {
                    $paths[$path] = true;
                }
            }
        }
        if (empty($paths)) {
            return;
        }

        $paths = array_keys($paths);
        $connection = $this->resourceConnection->getConnection();
        $configTable = $this->resourceConnection->getTableName("core_config_data");
        $deleted = $connection->delete($configTable, ["path IN (?)" => $paths]);

        if ($deleted > 0) {
            $output->writeln(
                "<info>Reset $deleted Luma sample-data config entries:</info>",
            );
            foreach ($paths as $path) {
                $output->writeln("  - $path");
            }
            $output->writeln("");
        }
    }

    /**
     * Create reset flag and clear patch_list entries so a single setup:upgrade can reinstall.
     *
     * Both side effects register undos: the flag file is deleted, and the
     * cleared patch_list rows are reinserted (by patch_name only — patch_id is
     * auto-increment and its value is not load-bearing for Magento's
     * "already applied" check).
     *
     * @param array<int, callable> $cleanups
     */
    private function prepareReinstall(OutputInterface $output, array &$cleanups): void
    {
        $varDir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $varDir->writeFile(self::RESET_FLAG_FILE, "");
        $cleanups[] = function () use ($varDir): void {
            if ($varDir->isExist(self::RESET_FLAG_FILE)) {
                $varDir->delete(self::RESET_FLAG_FILE);
            }
        };
        $output->writeln(
            "<info>Created var/" .
                self::RESET_FLAG_FILE .
                " — existing products, orders and customers will be removed on next setup:upgrade.</info>",
        );

        $connection = $this->resourceConnection->getConnection();
        $patchListTable = $this->resourceConnection->getTableName(
            "patch_list",
        );
        $clearedPatches = $connection->fetchCol(
            "SELECT patch_name FROM {$patchListTable} WHERE patch_name LIKE ?",
            ['Hyva\\\\KotiSampleData%'],
        );
        $deleted = $connection->delete($patchListTable, [
            "patch_name LIKE ?" => 'Hyva\\\\KotiSampleData%',
        ]);
        if (!empty($clearedPatches)) {
            $cleanups[] = function () use ($connection, $patchListTable, $clearedPatches): void {
                foreach ($clearedPatches as $patchName) {
                    $connection->insert($patchListTable, ['patch_name' => $patchName]);
                }
            };
        }
        if ($deleted > 0) {
            $output->writeln(
                "<info>Cleared $deleted sample data entries from patch_list.</info>",
            );
        }
        $output->writeln("");
    }

    /**
     * Remove Hyvä sample data packages from composer.json that are no longer available.
     *
     * If a previous deploy added packages that are now unavailable,
     * their presence in composer.json causes Composer to fail when resolving dependencies.
     *
     * @param array<string, string> $hyvaPackages Currently available packages (name => version)
     */
    private function removeStaleHyvaPackages(
        array $hyvaPackages,
        string $baseDir,
        OutputInterface $output,
    ): void {
        $composerFile = $baseDir . "composer.json";
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $composerData = json_decode(file_get_contents($composerFile), true);
        $required = $composerData["require"] ?? [];

        $stale = [];
        foreach ($required as $name => $version) {
            if (str_starts_with($name, self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX) && !isset($hyvaPackages[$name])) {
                $stale[] = $name;
            }
        }

        if (empty($stale)) {
            return;
        }

        $output->writeln(
            "<info>Removing stale Hyvä sample data packages from composer.json:</info>",
        );
        foreach ($stale as $name) {
            $output->writeln("  - $name");
        }
        $output->writeln("");

        $application = new ComposerApplication();
        $application->setAutoExit(false);
        $application->run(
            new ArrayInput([
                "command" => "remove",
                "packages" => $stale,
                "--working-dir" => $baseDir,
                "--no-interaction" => true,
                "--no-progress" => true,
                "--no-update" => true,
            ]),
            $output,
        );
    }

    /**
     * Query Composer repositories for available Hyvä sample data packages.
     *
     * Only composer- and path-type repositories are searched. VCS/git repos and
     * other types are skipped for speed (a VCS search can fetch or clone the remote)
     * and because they never host the sample data packages; public Packagist is
     * skipped as a wasted network call since these packages are private. Every other
     * composer repo is queried regardless of host, so a privately mirrored Hyvä
     * Packagist is not missed.
     *
     * IMPORTANT — a result here is a HINT, not ground truth for what `composer require`
     * can install. There are two independent reasons this check can report a package as
     * unavailable even though the require would install it fine:
     *
     *   1. Different environment. Magento's ComposerFactory forces COMPOSER_HOME to
     *      <magento-root>/var/composer_home (DirectoryList::COMPOSER_HOME), which is
     *      usually empty, so this in-process Composer is blind to the auth.json/config in
     *      the real user Composer home (~/.composer, COMPOSER_HOME, or one mounted by
     *      tooling such as Warden). A query against an authenticated repo like the Hyvä
     *      Packagist can therefore fail here for lack of credentials. (The shell `composer`
     *      CLI and the require in runComposerRequire() can each differ again.)
     *   2. Different endpoint. search() fetches the repo's root packages.json
     *      (loadRootServerFile()), whereas `composer require` resolves via per-package
     *      metadata (/p2/%package%.json). Different URL and cache state, so the root fetch
     *      can fail here while per-package resolution still works for the require.
     *
     * So a failed or empty check must NOT veto the require — the require is authoritative.
     * The second return value reports whether the check was *conclusive*: a path repo that
     * does not exist on this filesystem is benign (contributes nothing), but if any composer
     * repo we tried fails, the result is inconclusive (NOT "no packages exist") and the
     * caller falls back to requiring each package individually instead of dropping the ones
     * it could not verify. The scan does not abort on failure — every repo is tried so all
     * failures are reported.
     *
     * @return array{0: array<string, true>, 1: bool} [available package names as keys, conclusive]
     */
    private function getAvailableHyvaPackages(OutputInterface $output): array
    {
        $composer = $this->composerFactory->create();
        $available = [];
        $queriedRelevantRepo = false;
        $composerRepoFailed = false;

        foreach ($composer->getRepositoryManager()->getRepositories() as $repo) {
            $config = $repo->getRepoConfig();
            $url = $config["url"] ?? "";

            $isPathRepo = $repo instanceof PathRepository;
            $isComposerRepo = $repo instanceof ComposerRepository;

            // Only composer- and path-type repos can cheaply and meaningfully answer.
            // Everything else (vcs/git/github/gitlab/artifact/package/...) is skipped:
            // a VCS search may fetch or clone the remote, and none of those repos host
            // the Hyvä sample data packages. This keeps the check fast when a project
            // has many VCS repositories configured.
            if (!$isPathRepo && !$isComposerRepo) {
                continue;
            }

            // The sample data packages are private, so querying public Packagist is a
            // wasted network round trip. Skipping it by its stable host (rather than
            // allow-listing the Hyvä host) means any OTHER composer repo — including a
            // privately mirrored Hyvä Packagist on a different host — is still queried.
            if ($isComposerRepo && $this->isPublicPackagist($url)) {
                continue;
            }

            try {
                $results = $repo->search(
                    "^" .
                        preg_quote(self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX, "/"),
                    RepositoryInterface::SEARCH_NAME,
                );
            } catch (\RuntimeException $e) {
                if ($isPathRepo) {
                    // Path repo URL doesn't exist on this filesystem (e.g. inside Docker).
                    // Benign: it simply contributes no packages.
                    continue;
                }
                // A composer repo we intended to query failed (transport / auth). Record
                // it and keep going so every failing repo is reported, but the overall
                // result can no longer be authoritative: a package we would otherwise
                // drop might live in the repo we could not read.
                $composerRepoFailed = true;
                $output->writeln(sprintf(
                    "<comment>Warning: could not query %s for available Hyvä sample data " .
                        "packages (%s). Availability cannot be verified.</comment>",
                    $url,
                    $e->getMessage(),
                ));
                $output->writeln("");
                continue;
            }
            $queriedRelevantRepo = true;
            foreach ($results as $result) {
                $available[$result["name"]] = true;
            }
        }

        // Conclusive only if at least one relevant repository answered AND no composer
        // repo we tried failed. A single failed composer repo makes the result
        // inconclusive rather than pretending the packages it might hold don't exist;
        // the caller then falls back to per-package require instead of dropping them.
        // If no relevant repo is configured at all, availability cannot be verified.
        $conclusive = $queriedRelevantRepo && !$composerRepoFailed;

        if ($conclusive && empty($available)) {
            $output->writeln(
                "<comment>Warning: no Hyvä sample data packages found in the configured " .
                    "Composer repositories.</comment>",
            );
            $output->writeln("");
        }

        return [$available, $conclusive];
    }

    /**
     * Whether the given repository URL points at the public Packagist.
     *
     * Matched by host so it is unaffected by scheme, path or trailing slash.
     */
    private function isPublicPackagist(string $url): bool
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $host = parse_url($url, PHP_URL_HOST) ?: "";
        return in_array($host, ["repo.packagist.org", "packagist.org"], true);
    }

    /**
     * Run a single `composer require` for the given package specs.
     *
     * Each call runs in its own throwaway ComposerApplication, so no in-process
     * state carries over to the next call (e.g. the per-package retry loop) — the
     * next require reads composer.json fresh from disk. Composer itself reverts
     * composer.json when an update fails, so a failed require does not leave the
     * requested package behind.
     *
     * @param string[] $packages Package specs in "name:constraint" form.
     * @return int Composer's exit code (0 on success).
     */
    private function runComposerRequire(
        array $packages,
        string $baseDir,
        bool $noUpdate,
        OutputInterface $output,
    ): int {
        $arguments = [
            "command" => "require",
            "packages" => $packages,
            "--working-dir" => $baseDir,
            "--no-progress" => true,
        ];

        if ($noUpdate) {
            $arguments["--no-update"] = true;
        }

        $application = new ComposerApplication();
        $application->setAutoExit(false);

        return $application->run(new ArrayInput($arguments), $output);
    }

    /**
     * Raise memory_limit so Composer's dependency resolver can run in-process.
     *
     * Standalone `composer` sets memory_limit=-1 on startup, but our in-process
     * invocation via ComposerApplication inherits Magento's 128MB limit.
     * Mirrors Magento's own sampledata:deploy approach.
     */
    private function updateMemoryLimit(): void
    {
        if (function_exists("ini_set")) {
            $memoryLimit = trim(ini_get("memory_limit"));
            if ($memoryLimit !== "-1" && $this->getMemoryInBytes($memoryLimit) < 756 * 1024 * 1024) {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                ini_set("memory_limit", "756M");
            }
        }
    }

    /**
     * Convert PHP memory shorthand (128M, 1G) to bytes.
     */
    private function getMemoryInBytes(string $value): int
    {
        $unit = strtolower(substr($value, -1));
        $bytes = (int) $value;
        switch ($unit) {
            case "g":
                $bytes *= 1024 * 1024 * 1024;
                break;
            case "m":
                $bytes *= 1024 * 1024;
                break;
            case "k":
                $bytes *= 1024;
                break;
        }
        return $bytes;
    }

    /**
     * Map a Magento sample data package name to its Hyva equivalent.
     *
     * Pattern: magento/module-{X}-sample-data → hyva-themes/koti-sample-data-{X}
     */
    private function mapToHyvaPackage(string $magentoPackage): ?string
    {
        if (isset(self::EXPLICIT_PACKAGE_MAP[$magentoPackage])) {
            return self::EXPLICIT_PACKAGE_MAP[$magentoPackage];
        }
        if (preg_match(
            '#^magento/module-(.+)-sample-data$#',
            $magentoPackage,
            $m,
        )) {
            return self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX . $m[1];
        }
        return null;
    }
}
