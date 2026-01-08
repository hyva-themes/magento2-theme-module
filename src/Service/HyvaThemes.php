<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Service;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\View\Design\ThemeInterface;
use function array_filter as filter;
use function array_keys as keys;
use function array_map as map;
use function array_reverse as reverse;

/**
 * This class exists since release 1.3.18
 *
 * phpcs:disable Magento2.Functions.DiscouragedFunction
 */
class HyvaThemes
{
    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var string[]
     */
    private $hyvaBaseThemes;

    /**
     * @var bool[|
     */
    private $memoizedThemes = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        array $hyvaBaseThemes,
        ComponentRegistrar $componentRegistrar,
        Filesystem $filesystem
    ) {
        $this->hyvaBaseThemes = keys(filter($hyvaBaseThemes));
        $this->componentRegistrar = $componentRegistrar;
        $this->filesystem = $filesystem;
    }

    /**
     * @return string[]
     */
    public function getHyvaBaseThemes(): array
    {
        return $this->hyvaBaseThemes;
    }

    /**
     * Return true is the given theme is a Hyvä frontend theme.
     */
    public function isHyvaTheme(ThemeInterface $theme): bool
    {
        if ($path = $theme->getFullPath()) {
            return $this->isHyvaThemeCode($path);
        } else {
            return false;
        }
    }

    /**
     * Return true if the given theme code is a Hyvä frontend theme
     */
    public function isHyvaThemeCode(string $themeCode): bool
    {
        $themePath = count(explode('/', $themeCode)) === 3 ? $themeCode : 'frontend/' . $themeCode;
        if (!isset($this->memoizedThemes[$themePath])) {
            $inheritanceHierarchy = $this->getThemeHierarchy($themePath);
            $this->memoizedThemes[$themePath] = count(array_intersect($inheritanceHierarchy, $this->hyvaBaseThemes)) > 0;
        }
        return $this->memoizedThemes[$themePath];
    }

    private function getThemeHierarchy(string $themePath): array
    {
        $hierarchy = [];
        do {
            $hierarchy[] = $themePath;
        } while ($themePath = $this->determineParentThemePath($themePath));
        return reverse(map([$this, 'removeAreaFromCode'], $hierarchy));
    }

    /**
     * Return only the theme code without the area prefix
     */
    private function removeAreaFromCode(string $themePath): string
    {
        $parts = explode('/', $themePath);

        return $parts[1] . '/' . $parts[2];
    }

    private function determineParentThemePath(string $themeCode): string
    {
        // Guard against themes not being present any more or registered in the DB with the wrong area
        if (!($themePath = $this->componentRegistrar->getPath(ComponentRegistrar::THEME, $themeCode))) {
            return '';
        }
        $xml = $this->slurp($themePath . '/theme.xml');
        return preg_match('#<parent>\s*(?<parentTheme>[^<\s]+)\s*</parent>#im', $xml, $matches)
            ? explode('/', $themeCode)[0] . '/' . $matches['parentTheme']
            : '';
    }

    private function slurp(string $filePath): string
    {
        $filename = basename($filePath);
        $read = $this->filesystem->getDirectoryReadByPath(dirname($filePath), DriverPool::FILE);

        return $read->isExist($filename) && $read->isReadable($filename)
            ? $read->readFile($filename)
            : '';
    }
}
