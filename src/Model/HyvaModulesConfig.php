<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\App\Area as AppArea;
use Magento\Framework\App\Bootstrap as AppBootstrap;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\ObjectManager\ConfigLoaderInterface;
use Magento\Framework\ObjectManagerInterface;
use function array_merge as merge;
use function array_reduce as reduce;
use function array_values as values;

class HyvaModulesConfig
{
    public const FILE = 'hyva-themes.json';
    public const PATH = 'app/etc';

    /**
     * @var File
     */
    private $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public function generateFile(string $outputFile = self::FILE, string $targetPath = self::PATH): string
    {
        $path     = trim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $fullPath = BP . DIRECTORY_SEPARATOR . $path . strtolower($outputFile);
        $this->validatePath($fullPath);

        $this->file->write($fullPath, $this->getJson());

        return $fullPath;
    }

    public function getJson(): string
    {
        return json_encode($this->gatherConfigData(), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES);
    }

    private function validatePath(string $fullPath): void
    {
        $pathinfo = $this->file->getPathInfo($fullPath);

        if (!isset($pathinfo['extension']) || strtolower($pathinfo['extension']) !== 'json') {
            throw new \InvalidArgumentException('Only .json configuration is supported.');
        }
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
     * Any module registered with the \Hyva\CompatModuleFallback\Model\CompatModuleRegistry is added automatically.
     *
     * Note: more records besides "src" might be added in the future.
     *
     * @see \Hyva\CompatModuleFallback\Observer\HyvaThemeHyvaConfigGenerateBefore::execute()
     */
    public function gatherConfigData(): array
    {
        // Keep reference to current ObjectManager instance, so it can be reset later
        $currentObjectManager = ObjectManager::getInstance();

        $newObjectManager = $this->createObjectManagerWithCurrentModulesList();
        $eventManager     = $newObjectManager->create(EventManagerInterface::class);
        $configContainer  = new DataObject();
        $eventManager->dispatch('hyva_config_generate_before', ['config' => $configContainer]);

        // Revert ObjectManager to reset app state
        ObjectManager::setInstance($currentObjectManager);

        // Merge records with the same src (could happen for compat modules that also add an observer)
        $data = $configContainer->getData();
        $data['extensions'] = values(reduce($data['extensions'] ?? [], function (array $acc, array $record): array {
            if ($path = $record['src'] ?? false) {
                $acc[$path] = merge($acc[$path] ?? [], $record);
            }
            return $acc;
        }, []));
        return $data;
    }

    private function createObjectManagerWithCurrentModulesList(): ObjectManagerInterface
    {
        $objectManagerFactory = AppBootstrap::createObjectManagerFactory(BP, []);
        $objectManager        = $objectManagerFactory->create([]);
        $this->initFrontendArea($objectManager);

        return $objectManager;
    }

    private function initFrontendArea(ObjectManagerInterface $objectManager): void
    {
        $objectManager->get(AppState::class)->setAreaCode(AppArea::AREA_FRONTEND);
        $configLoader = $objectManager->get(ConfigLoaderInterface::class);
        $objectManager->configure($configLoader->load(AppArea::AREA_FRONTEND));
    }
}
