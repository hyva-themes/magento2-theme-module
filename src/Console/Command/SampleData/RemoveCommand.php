<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Console\Command\SampleData;

use Composer\Console\Application as ComposerApplication;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\Console\Cli;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Remove all installed Hyvä sample data packages.
 */
class RemoveCommand extends Command
{
    private const HYVA_PACKAGE_PREFIX = "hyva-themes/koti-sample-data-";

    public const OPTION_NO_UPDATE = "no-update";

    private Filesystem $filesystem;
    private ComposerInformation $composerInformation;

    public function __construct(
        Filesystem $filesystem,
        ComposerInformation $composerInformation,
    ) {
        $this->filesystem = $filesystem;
        $this->composerInformation = $composerInformation;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName("hyva:sampledata:remove")->setDescription(
            "Remove all Hyvä sample data packages from composer.json",
        );
        $this->addOption(
            self::OPTION_NO_UPDATE,
            null,
            InputOption::VALUE_NONE,
            "Update composer.json without executing composer update",
        );
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->updateMemoryLimit();

        $installedPackages = $this->composerInformation->getInstalledMagentoPackages();
        $toRemove = [];
        foreach ($installedPackages as $name => $info) {
            if (str_starts_with($name, self::HYVA_PACKAGE_PREFIX)) {
                $toRemove[] = $name;
            }
        }

        if (empty($toRemove)) {
            $output->writeln(
                "<info>No Hyvä sample data packages are currently installed.</info>",
            );
            return Cli::RETURN_SUCCESS;
        }

        $output->writeln("<info>Removing Hyvä sample data packages:</info>");
        foreach ($toRemove as $name) {
            $output->writeln("  - $name");
        }
        $output->writeln("");

        $baseDir = $this->filesystem
            ->getDirectoryRead(DirectoryList::ROOT)
            ->getAbsolutePath();
        $arguments = [
            "command" => "remove",
            "packages" => $toRemove,
            "--working-dir" => $baseDir,
            "--no-interaction" => true,
            "--no-progress" => true,
        ];

        if ($input->getOption(self::OPTION_NO_UPDATE)) {
            $arguments["--no-update"] = true;
        }

        $application = new ComposerApplication();
        $application->setAutoExit(false);
        $result = $application->run(new ArrayInput($arguments), $output);

        if ($result !== 0) {
            $output->writeln(
                "<error>Error during Hyvä sample data removal.</error>",
            );
            return Cli::RETURN_FAILURE;
        }

        $output->writeln("");
        $output->writeln(
            "<info>Hyvä sample data packages have been removed.</info>",
        );

        return Cli::RETURN_SUCCESS;
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
}
