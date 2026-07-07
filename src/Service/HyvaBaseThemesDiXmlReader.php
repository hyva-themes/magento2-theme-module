<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Service;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Component\ComponentRegistrar;
use Psr\Log\LoggerInterface;

use function array_filter as filter;
use function array_keys as keys;
use function array_merge as merge;

/**
 * Recovers the `hyvaBaseThemes` DI argument for \Hyva\Theme\Service\HyvaThemes directly from the
 * etc/di.xml source files of all enabled modules, bypassing the object manager and every config cache.
 *
 * This class exists for one scenario only: setup:di:compile reads the merged di.xml configuration through
 * the config cache (cache id `global::DiConfig`), which is keyed by scope name alone and is not invalidated
 * when module files change. When that cache entry was written while this module's di.xml was not part of
 * the merge (for example by a bootstrap during the first setup:upgrade after composer installed the module,
 * before the module list in app/etc/config.php was updated), the compiler finds no value for the required
 * `hyvaBaseThemes` array argument and records an explicit null in generated/metadata. The compiled object
 * manager then passes literal null to the HyvaThemes constructor.
 *
 * Reading the current di.xml files directly restores the values that setup:di:compile should have compiled,
 * including any base themes added or disabled by other modules. See HyvaThemes::__construct() for when this
 * fallback is triggered.
 *
 * The dependencies of this class are deliberately limited to classes that survive the same stale-cache
 * scenario: concrete framework classes are auto-wired by the compiler even without any configuration, and
 * the LoggerInterface preference is declared in app/etc/di.xml, which is part of every merge. Do not add
 * dependencies that require module di.xml configuration to resolve.
 *
 * phpcs:disable Magento2.Functions.DiscouragedFunction
 */
class HyvaBaseThemesDiXmlReader
{
    /**
     * Failsafe used when the di.xml sources cannot be read at all, for example when this module is
     * missing from app/etc/config.php. Also serves as the base layer for the merge, since the same
     * entries are declared in this module's own etc/di.xml.
     */
    private const BUILT_IN_HYVA_BASE_THEMES = [
        'Hyva/reset' => true,
        'Hyva/default' => true,
        'Hyva/default-csp' => true,
    ];

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ComponentRegistrar $componentRegistrar,
        DeploymentConfig $deploymentConfig,
        LoggerInterface $logger
    ) {
        $this->componentRegistrar = $componentRegistrar;
        $this->deploymentConfig = $deploymentConfig;
        $this->logger = $logger;
    }

    /**
     * Return the merged `hyvaBaseThemes` argument value as configured in the enabled modules' etc/di.xml files.
     *
     * The result has the same shape as the di.xml argument: a map of theme path => enabled flag. Entries from
     * modules later in the app/etc/config.php sequence override earlier entries with the same item name, which
     * matches how Magento merges array arguments. Only the global etc/di.xml files are read; area-specific
     * declarations of the argument (e.g. etc/frontend/di.xml) are not picked up.
     *
     * This method never throws. On any failure it returns the built-in Hyvä base themes.
     *
     * @return bool[] map of theme path (e.g. "Hyva/default") to enabled flag
     */
    public function readBaseThemesFromDiXmlSources(): array
    {
        $this->logger->warning(
            'HyvaThemes was instantiated without the hyvaBaseThemes di.xml argument - the compiled DI '
            . 'configuration in generated/metadata is stale. Recovering the value from the di.xml source files. '
            . 'To repair the compiled configuration, flush the config cache and run setup:di:compile again.'
        );
        try {
            return $this->mergeBaseThemesFromDiXmlFiles($this->getEnabledModuleDiXmlFiles());
        } catch (\Throwable $exception) {
            $this->logger->error(
                sprintf('Unable to read hyvaBaseThemes from di.xml sources: %s', $exception->getMessage())
            );
            return self::BUILT_IN_HYVA_BASE_THEMES;
        }
    }

    /**
     * Extract and merge the `hyvaBaseThemes` argument from the given di.xml files, in the given order.
     *
     * Later files override earlier entries with the same item name. Files that do not exist, cannot be
     * parsed, or do not declare the argument are skipped. Item values are interpreted like the di.xml
     * boolean type: "false" and "0" disable an entry, anything else enables it.
     *
     * @param string[] $diXmlFiles absolute file paths in module sequence order
     * @return bool[] map of theme path to enabled flag
     */
    public function mergeBaseThemesFromDiXmlFiles(array $diXmlFiles): array
    {
        $baseThemes = self::BUILT_IN_HYVA_BASE_THEMES;
        foreach ($diXmlFiles as $diXmlFile) {
            $baseThemes = merge($baseThemes, $this->extractBaseThemesArgument($diXmlFile));
        }
        return $baseThemes;
    }

    /**
     * Return the etc/di.xml file paths of all enabled modules, in app/etc/config.php sequence order.
     *
     * The sequence order matters because it is the order in which Magento merges the module configuration
     * files, and later declarations override earlier array items with the same name.
     *
     * @return string[]
     */
    private function getEnabledModuleDiXmlFiles(): array
    {
        $modules = $this->deploymentConfig->get('modules') ?? [];
        $files = [];
        foreach (keys(filter($modules)) as $moduleName) {
            $modulePath = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, (string) $moduleName);
            if ($modulePath && file_exists($modulePath . '/etc/di.xml')) {
                $files[] = $modulePath . '/etc/di.xml';
            }
        }
        return $files;
    }

    /**
     * Extract the `hyvaBaseThemes` argument items declared for HyvaThemes in a single di.xml file.
     *
     * @param string $diXmlFile
     * @return bool[] map of theme path to enabled flag, empty if the file declares nothing relevant
     */
    private function extractBaseThemesArgument(string $diXmlFile): array
    {
        if (!is_file($diXmlFile)) {
            return [];
        }
        $previousErrorHandling = libxml_use_internal_errors(true);
        try {
            $document = new \DOMDocument();
            if (!$document->load($diXmlFile)) {
                return [];
            }
            return $this->extractBaseThemesFromDocument($document);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorHandling);
        }
    }

    /**
     * Extract the `hyvaBaseThemes` argument items from a parsed di.xml document.
     *
     * @param \DOMDocument $document
     * @return bool[] map of theme path to enabled flag
     */
    private function extractBaseThemesFromDocument(\DOMDocument $document): array
    {
        $xpath = new \DOMXPath($document);
        $items = [];
        $query = '/config/type/arguments/argument[@name="hyvaBaseThemes"]/item';
        foreach ($xpath->query($query) as $itemNode) {
            $typeNode = $itemNode->parentNode->parentNode->parentNode;
            if (!$typeNode instanceof \DOMElement) {
                continue;
            }
            // Leading backslashes in type names are tolerated by Magento, so normalize before comparing
            if (ltrim($typeNode->getAttribute('name'), '\\') !== HyvaThemes::class) {
                continue;
            }
            $themePath = $itemNode->getAttribute('name');
            if ($themePath !== '') {
                $items[$themePath] = !in_array(trim($itemNode->nodeValue), ['false', '0'], true);
            }
        }
        return $items;
    }
}
