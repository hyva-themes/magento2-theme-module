<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Service;

use Magento\Framework\App\ObjectManager;
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
 * The $hyvaBaseThemes constructor argument is nullable even though src/etc/di.xml always declares it:
 *
 * setup:di:compile does not read the di.xml files directly. It consumes the merged DI configuration
 * through the config cache (cache id `global::DiConfig`), which is keyed by scope name alone and is not
 * invalidated when files change. When that cache entry was written at a moment this module's di.xml was
 * not part of the merge — typically by a Magento bootstrap during the first setup:upgrade after composer
 * installed the module, while app/etc/config.php did not list the module yet — the compiler finds no
 * value for the required array argument and records an explicit null in generated/metadata. The compiled
 * object manager then passes literal null to this constructor. The same happens to the explicit
 * `hyvaThemes` injections into the minifier plugins, whose in-constructor fallback then builds this
 * service via ObjectManager::get() with the same broken compiled arguments. Only array and scalar
 * arguments are affected; object arguments are auto-wired by the compiler even without configuration.
 *
 * Since the minifier disable plugins resolve this service for every asset, a null argument would
 * otherwise fatal on every file of a setup:static-content:deploy run.
 *
 * When null is injected, the constructor falls back to HyvaBaseThemesDiXmlReader, which re-reads the
 * `hyvaBaseThemes` argument from the enabled modules' etc/di.xml source files, bypassing the object
 * manager and all caches. This recovers the same merged value the compiler should have produced —
 * including custom base themes that projects add through their own di.xml — and logs a warning, since
 * the stale compiled configuration should still be repaired by flushing the config cache and running
 * setup:di:compile again.
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
     * @var bool[]
     */
    private $memoizedThemes = [];

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * The $hyvaBaseThemes argument is nullable to guard against stale compiled DI configuration passing
     * null instead of the configured array — see the class comment for the full explanation. The reader
     * argument is nullable for backward compatibility (this class shipped in prior releases); it is only
     * resolved, and the di.xml sources are only read, when $hyvaBaseThemes actually arrives as null.
     *
     * @param bool[]|null $hyvaBaseThemes map of theme path to enabled flag, as declared in di.xml
     * @param ComponentRegistrar $componentRegistrar
     * @param Filesystem $filesystem
     * @param HyvaBaseThemesDiXmlReader|null $baseThemesDiXmlReader
     */
    public function __construct(
        ?array $hyvaBaseThemes,
        ComponentRegistrar $componentRegistrar,
        Filesystem $filesystem,
        ?HyvaBaseThemesDiXmlReader $baseThemesDiXmlReader = null
    ) {
        if ($hyvaBaseThemes === null) {
            $baseThemesDiXmlReader = $baseThemesDiXmlReader
                ?? ObjectManager::getInstance()->get(HyvaBaseThemesDiXmlReader::class);
            $hyvaBaseThemes = $baseThemesDiXmlReader->readBaseThemesFromDiXmlSources();
        }
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
