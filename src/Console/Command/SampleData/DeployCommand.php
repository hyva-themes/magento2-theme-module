<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Console\Command\SampleData;

use Composer\Console\Application as ComposerApplication;
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

            $available = $this->getAvailableHyvaPackages($output);
            $unavailable = [];
            if (!empty($available)) {
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
            $result = $application->run(new ArrayInput($arguments), $output);

            if ($result !== 0) {
                $output->writeln(
                    "<error>Error during Hyvä sample data deployment. Composer changes will be reverted.</error>",
                );
                $application->resetComposer();
                return Cli::RETURN_FAILURE;
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
     * Searches the Hyvä private packagist and local path repositories.
     * Skips packagist.org and other remote repos to avoid unnecessary network calls.
     *
     * @return array<string, true> Package names as keys
     */
    private function getAvailableHyvaPackages(OutputInterface $output): array
    {
        $composer = $this->composerFactory->create();
        $available = [];

        foreach ($composer->getRepositoryManager()->getRepositories() as $repo) {
            $config = $repo->getRepoConfig();
            $url = $config["url"] ?? "";
            $type = $config["type"] ?? "";

            $isHyvaRepo = str_starts_with(
                $url,
                "https://hyva-themes.repo.packagist.com",
            );
            $isPathRepo = $type === "path";

            if (!$isHyvaRepo && !$isPathRepo) {
                continue;
            }

            try {
                $results = $repo->search(
                    "^" .
                        preg_quote(self::HYVA_SAMPLE_DATA_PACKAGE_PREFIX, "/"),
                    RepositoryInterface::SEARCH_NAME,
                );
            } catch (\RuntimeException) {
                // Path repo URL doesn't exist on this filesystem (e.g. inside Docker)
                continue;
            }
            foreach ($results as $result) {
                $available[$result["name"]] = true;
            }
        }

        if (empty($available)) {
            $output->writeln(
                "<comment>Warning: No Hyvä packages found in any Composer repository. " .
                    "Skipping availability filter.</comment>",
            );
            $output->writeln("");
        }

        return $available;
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
