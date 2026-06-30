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
     * Default list of Hyvä base themes, used as a fallback when the configured value is absent.
     *
     * Observed problem: during `bin/magento setup:static-content:deploy -j <N>` (the multi-process
     * variant that forks worker processes), this service can be instantiated with
     * $hyvaBaseThemes === null even though src/etc/di.xml declares the `hyvaBaseThemes` array
     * argument. The object manager passes null where the configured array is expected, so the
     * required `array` parameter fatals with "Argument #1 ($hyvaBaseThemes) must be of type array,
     * null given". Because the minifier plugin builds this service for every asset, that turns into
     * a failure on every file and the whole deploy fails.
     *
     * We do NOT know the fundamental reason the di.xml argument is not passed by the object manager
     * in this scenario. It has only been observed with multiple static-content-deploy workers; in
     * normal/single-process setups the argument is compiled and resolved correctly, and we have not
     * been able to reproduce or explain why the configured value goes missing specifically under the
     * forked workers. Rather than let the deploy fatal, the constructor accepts a nullable value and
     * coalesces to this constant.
     *
     * LIMITATION — this is a crash guard, not a full fix. `hyvaBaseThemes` is intentionally
     * configurable in di.xml so projects can register their own custom base themes. This constant
     * only contains the built-in Hyvä base themes, so when the fallback is hit (the object manager
     * did not pass the configured array) any CUSTOM base themes added via di.xml are absent from the
     * list and will NOT be recognized — their assets get minified as if they were not Hyvä themes.
     * So the fallback prevents the fatal but does not restore correct behaviour for projects with
     * custom base themes.
     *
     * For projects with custom base themes the only known stable workaround is to run
     * `setup:static-content:deploy` with a single worker (`-j 1`, or omit `-j`), which avoids
     * triggering the missing-argument condition altogether. That guidance stands until the root
     * cause (why the object manager drops the configured argument under forked workers) is found.
     *
     * Keep this list in sync with the `hyvaBaseThemes` argument in src/etc/di.xml.
     */
    private const DEFAULT_HYVA_BASE_THEMES = [
        'Hyva/reset' => true,
        'Hyva/default' => true,
        'Hyva/default-csp' => true,
    ];

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
        ?array $hyvaBaseThemes,
        ComponentRegistrar $componentRegistrar,
        Filesystem $filesystem
    ) {
        $this->hyvaBaseThemes = keys(filter($hyvaBaseThemes ?? self::DEFAULT_HYVA_BASE_THEMES));
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
