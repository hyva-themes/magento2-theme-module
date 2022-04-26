<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Console\Command;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Console\Cli;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/** @SuppressWarnings(PHPMD.CouplingBetweenObjects) */

class HyvaConfigGenerate extends Command
{
    const FILE = 'hyva-themes.json';
    const PATH = 'app/etc';

    /** @var File $file */
    private $file;
    /** @var EventManagerInterface $eventManager */
    private $eventManager;
    /** @var ObjectManagerInterface $objectManager */
    private $objectManager;
    /** @var AppState $appState */
    private $appState;

    /**
     * @param File $file
     * @param EventManagerInterface $eventManager
     * @param ObjectManagerInterface $objectManager
     * @param AppState $appState
     * @param string|null $name
     */
    public function __construct(
        File $file,
        EventManagerInterface $eventManager,
        ObjectManagerInterface $objectManager,
        AppState $appState,
        string $name = null
    ) {
        parent::__construct($name);

        $this->file = $file;
        $this->eventManager = $eventManager;
        $this->objectManager = $objectManager;
        $this->appState = $appState;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('hyva:config:generate');
        $this->setDescription('Generate Hyvä Themes configuration file');

        $options = [
            new InputOption('file', null, InputOption::VALUE_OPTIONAL, 'File name'),
            new InputOption('path', null, InputOption::VALUE_OPTIONAL, 'File path (from Magento Base dir)'),
            new InputOption('info', null, InputOption::VALUE_NONE, 'More information'),
            new InputOption('force', 'f', InputOption::VALUE_NONE, 'Force if the file already exists')
        ];

        $this->setDefinition($options);
        parent::configure();
    }

    /**
     * Generate a Hyvä Themes configuration file.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null|int
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        if ($input->getOption('info')) {
            return $this->help($output);
        }

        $this->loadAreaConfig(Area::AREA_FRONTEND);

        $file = $input->getOption('file') ?? self::FILE;
        $path = (string) ($input->getOption('path') ?? self::PATH);

        $path = trim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $fullpath = BP . DIRECTORY_SEPARATOR . $path . strtolower($file);
        $pathinfo = $this->file->getPathInfo($fullpath);

        try {
            if ($this->file->fileExists($fullpath) && !$input->getOption('force')) {
                throw new InvalidArgumentException('The configuration file already exists. Use -f to overwrite it.');
            }
            if (!isset($pathinfo['extension']) || strtolower($pathinfo['extension']) !== 'json') {
                throw new InvalidArgumentException('Only .json configuration is supported.');
            }

            $this->writeConfigFile($fullpath);
        } catch (Exception $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            return Cli::RETURN_FAILURE;
        }

        $output->writeln('<info>Hyvä Themes - Configuration file generated at \'' . $fullpath . '\'.</info>');
        return Cli::RETURN_SUCCESS;
    }

    /**
     * Gather the data for and write the app/etc/hyva-themes.json config file
     *
     * The config structure is an array of all modules that want to hook into the tailwind styles build process.
     *
     * [
     *   "extensions": [
     *     ["src" => "vendor/vendor-name/magento2-module-name/src"],
     *     ...
     *   ]
     * ]
     *
     * Modules can add themselves to the list using the event "hyva_config_generate_before".
     *
     * Note: more records besides "src" might be added in the future.
     *
     * @param string $fullpath
     * @see \Hyva\CompatModuleFallback\Observer\HyvaThemeHyvaConfigGenerateBefore::execute()
     */
    private function writeConfigFile(string $fullpath): void
    {
        $configObject = new DataObject();
        $this->eventManager->dispatch('hyva_config_generate_before', ['config' => $configObject]);

        $this->file->write($fullpath, json_encode($configObject->getData(), \JSON_PRETTY_PRINT));
    }

    /**
     * @param string $code
     * @throws LocalizedException
     */
    private function loadAreaConfig(string $code)
    {
        $this->appState->setAreaCode($code);

        /** @var ConfigLoaderInterface $configLoader */
        $configLoader = $this->objectManager->get(ConfigLoaderInterface::class);
        $this->objectManager->configure($configLoader->load($code));
    }

    private function help(OutputInterface $output): int
    {
        $output->writeln('<info>Go check our documentation at https://docs.hyva.io</info>');
        return Cli::RETURN_SUCCESS;
    }
}
