<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Math\Random as MathRandom;
use Magento\Framework\View\Asset;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction

/**
 * This generic SvgIcons view model can be used to render any icon set (i.e. subdirectory in web/svg).
 *
 * It is used as part of the Hyva_Theme module to provide the Heroicon view models, and to provide access to
 * custom SVG icons inside Hyvä themes.
 * This class is also intended to be used as a base class for other modules providing icon sets to Hyvä themes.
 *
 * Possible di.xml customization points:
 *
 * iconPathPrefix (default: Hyva_Theme::svg)
 *
 * The iconPathPrefix will be prepended before the specified icon.
 * For example, given an iconPrefix of `My_Module::svg`, a call to `renderHtml('my-icon') renders the first existing of
 *
 * 1. <current-theme>/My_Module/web/svg/my-icon.svg
 * 2. My_Module/view/frontend/web/svg/my-icon.svg
 * 2. My_Module/view/base/web/svg/my-icon.svg
 * 3. <current-theme>/web/svg/my-icon.svg
 *
 * It is possible to specify subdirectories as part of the iconPathPrefix, as in `My_Module::svg/light`.
 *
 * For an example, please refer to the constructor arguments configured for Hyva\Theme\ViewModel\HeroiconsSolid in
 * the file hyva-themes/magento2-theme-module/etc/frontend/di.xml
 *
 * The Icon View Models included within the hyva_Theme module use the following iconPathPrefixes:
 * - SvgIcons: Hyva_Theme::svg
 * - HeroiconsSolid: Hyva_Theme::svg/heroicons/solid
 * - HeroiconsOutline: Hyva_Theme::svg/heroicons/outline
 *
 * pathPrefixMapping (optional)
 *
 * The pathPrefixMapping allows modules to register an alias for their "iconPathPrefix".
 * This is useful for providing more readable icon names to the CMS content template processor.
 * For example, the icon {{icon "sport-icons/outline/darts"}} can be mapped to the icon
 * asset path "Hyva_SportIcons::svg/outline/darts".
 *
 * The pathPrefixMapping is applied by using the first part of the icon path ("sport-icons" in the example above)
 * as the map lookup key. If a value is present, it will be used. If no value is present in the map,
 * the iconPathPrefix is used (see above).
 *
 *
 * iconSet (optional)
 *
 * If set, the iconSet is injected between the path prefix and the remainder of the icon path.
 * For example, assuming the iconPathPrefix is `My_Module::svg/awesome` and the iconSet is 'outline', then
 * calling $icon->renderHtml('box') would render `My_Module::svg/awwesome/outline/box`.
 *
 * Note: there is some duplication in functionality between the iconPathPrefix and the iconSet.
 * In the previous example an iconPathPrefix of `My_Module::svg/awesome/outline` could also be used.
 * The reason this overlap of functionality is present is to preserve backward compatibility.
 *
 * The hyva-themes/magento2-theme-module ships with two Heroicon iconsets and matching view models:
 *
 * @see HeroiconsSolid
 * @see HeroiconsOutline
 */
class SvgIcons implements ArgumentInterface
{
    public const CACHE_TAG = 'HYVA_ICONS';

    /**
     * Module name prefix for icon asset, e.g. Hyva_Theme::svg
     *
     * @var string
     */
    private $iconPathPrefix;

    /**
     * Human friendly alias for iconPathPrefixes
     *
     * @var string[]
     */
    private $pathPrefixMapping;

    /**
     * Optional folder name to be appended to the $iconPathPrefix
     *
     * @var string
     */
    private $iconSet = '';

    /**
     * @var Asset\Repository
     */
    private $assetRepository;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var bool
     */
    private $isSvgIconCacheEnabled;

    /**
     * Global counter for how many times SVG internal IDs is rendered.
     *
     * @var  int[]
     * @see self::disambiguateSvgIds
     */
    private static $internalIdUsageCounts = [];

    public function __construct(
        Asset\Repository $assetRepository,
        CacheInterface $cache,
        DesignInterface $design,
        string $iconPathPrefix = 'Hyva_Theme::svg',
        string $iconSet = '',
        array $pathPrefixMapping = [],
        ?ScopeConfigInterface $scopeConfig = null
    ) {
        $this->assetRepository = $assetRepository;
        $this->cache = $cache;
        $this->design = $design;
        $this->iconPathPrefix = rtrim($iconPathPrefix, '/');
        $this->iconSet = $iconSet;
        $this->pathPrefixMapping = $pathPrefixMapping;
        $this->scopeConfig = $scopeConfig ?? ObjectManager::getInstance()->get(ScopeConfigInterface::class);
    }

    /**
     * Renders an inline SVG icon from the configured icon set
     *
     * The method ends with Html instead of Svg so that the Magento code sniffer understands it is safe HTML and does
     * not need to be escaped.
     *
     * @param string $icon The SVG file name without .svg suffix
     * @param string $classNames CSS classes to add to the root element, space separated
     * @param int|null $width Width in px (recommended to render in correct size without CSS)
     * @param int|null $height Height in px (recommended to render in correct size without CSS)
     * @param array $attributes Additional attributes you can set on the SVG as key => value, like :class for AlpineJS
     * @return string
     */
    public function renderHtml(
        string $icon,
        string $classNames = '',
        ?int $width = 24,
        ?int $height = 24,
        array $attributes = []
    ): string {
        if (!$this->isAriaHidden($attributes) && !isset($attributes['role'])) {
            $attributes['role'] = 'img';
        }

        $iconPath = $this->applyPathPrefixAndIconSet($icon);

        $cacheKey = $this->getCacheKey($iconPath, $classNames, $width, $height, $attributes);

        if ($this->isSvgCacheEnabled()) {
            $cacheKey = $this->getCacheKey($iconPath, $classNames, $width, $height, $attributes);
            if ($result = $this->cache->load($cacheKey)) {
                return $this->withMaskedAlpineAttributes($result, [$this, 'disambiguateSvgIds']);
            }
        }

        try {
            $rawIconSvg = \file_get_contents($this->getFilePath($iconPath)); // phpcs:disable
            $result = $this->withMaskedAlpineAttributes($rawIconSvg, function (string $rawIconSvg) use ($icon, $attributes, $height, $width, $classNames): string {
                return $this->applySvgArguments($rawIconSvg, $classNames, $width, $height, $attributes, $icon);
            });
            if ($this->isSvgCacheEnabled()) {
                $this->cache->save($result, $cacheKey, [self::CACHE_TAG]);
            }

            return $this->withMaskedAlpineAttributes($result, [$this, 'disambiguateSvgIds']);
        } catch (Asset\File\NotFoundException $exception) {
            $error = (string) __('Unable to find the SVG icon "%1"', $icon);
            throw new Asset\File\NotFoundException($error, 0, $exception);
        }
    }

    private function isSvgCacheEnabled(): bool
    {
        if (! isset($this->isSvgIconCacheEnabled)) {
            $this->isSvgIconCacheEnabled = (bool) $this->scopeConfig->getValue('hyva_theme_dev/cache/enable_svg_icon_caching');
        }
        return $this->isSvgIconCacheEnabled;
    }

    private function disambiguateSvgIds(string $svgContent): string
    {
        $svgXml = new \SimpleXMLElement($svgContent);
        $idAttributes = $svgXml->xpath('/*/*//@id');
        $uniqueIdList = [];
        foreach ($idAttributes as $idAttr) {
            $id = (string) $idAttr->id;
            if (isset(self::$internalIdUsageCounts[$id])) {
                $uniqueId = $id . '_' . (++self::$internalIdUsageCounts[$id]);
                $uniqueIdList['/#' . preg_quote($id, '/') . '\b/'] = '#' . $uniqueId;
                $idAttr->id = $uniqueId;
            } else {
                self::$internalIdUsageCounts[$id] = 1;
            }
        }

        $svgContent = \str_replace("<?xml version=\"1.0\"?>\n", '', $svgXml->asXML());

        // A simple str_replace will also match hex colors that start with the same string as the id, e.g. id="#b" fill="#bcd".
        // We use preg_replace with a word boundary token at the end of the ID to make the match less likely.
        return preg_replace(array_keys($uniqueIdList), array_values($uniqueIdList), $svgContent);
    }

    /**
     * Magic method to allow iconNameHtml() instead of renderHtml('icon-name'). Subclasses may
     * use `@method` doc blocks to provide autocompletion for available icons.
     */
    public function __call($method, $args)
    {
        if (\preg_match('/^(.*)Html$/', $method, $matches)) {
            return $this->renderHtml(self::camelCaseToKebabCase($matches[1]), ...$args);
        }
        return '';
    }

    /**
     * Convert a CamelCase string into kebab-case
     *
     * For example ArrowUp => arrow-up
     *
     * @SuppressWarnings(PHPMD.ShortVariable)
     */
    private static function camelCaseToKebabCase(string $str): string
    {
        return strtolower(preg_replace('/(.|[0-9])([A-Z]|[0-9])/', "$1-$2", $str));
    }

    /**
     * Return absolute path to icon file, respecting theme fallback.
     *
     * If no matching icon within the given iconPathPrefix module is found, it will fall back to the theme web folder.
     */
    private function getFilePath(string $iconPath): string
    {
        $asset = $this->assetRepository->createAsset($iconPath);
        try {
            // try to locate asset with iconPathPrefix (e.g. Hyva_Theme::svg)
            $path = $asset->getSourceFile();
        } catch (Asset\File\NotFoundException $exception) {
            // fallback to web/ folder in current theme if not found
            $path = $this->assetRepository->createAsset($asset->getFilePath())->getSourceFile();
        }
        return $path;
    }

    private function applyPathPrefixAndIconSet(string $icon): string
    {
        $iconSet = $this->iconSet ? $this->iconSet . '/' : '';
        $iconPathParts = explode('/', $icon, 2);

        return count($iconPathParts) === 2 && isset($this->pathPrefixMapping[$iconPathParts[0]])
            ? $this->pathPrefixMapping[$iconPathParts[0]] . '/' . $iconSet . $iconPathParts[1] . '.svg'
            : $this->iconPathPrefix . '/' . $iconSet . $icon . '.svg';
    }

    private function applySvgArguments(
        string $origSvg,
        string $classNames,
        ?int $width,
        ?int $height,
        array $attributes,
        string $icon
    ): string {
        $svgXml = new \SimpleXMLElement($origSvg);
        if (trim($classNames)) {
            $svgXml['class'] = $classNames;
        }
        if ($width) {
            $svgXml['width'] = (string) $width;
        }
        if ($height) {
            $svgXml['height'] = (string) $height;
        }

        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                if (!empty($key) && $key !== 'title' && !isset($svgXml[strtolower($key)])) {
                    $svgXml[strtolower($key)] = is_bool($value)
                        ? ($value ? 'true' : 'false')
                        : (string) $value;
                }
            }
        }

        if (!$this->isAriaHidden($attributes) && ! $this->hasTitle($svgXml)) {
            $svgXml->title = (string) ($attributes['title'] ?? $icon);
        }

        $xml = $svgXml->asXML();

        return \str_replace("<?xml version=\"1.0\"?>\n", '', $xml);
    }

    private function hasTitle(\SimpleXMLElement  $svgXml): bool
    {
        foreach ($svgXml->children() as $child) {
            if ($child->getName() === 'title') {
                return true;
            }
        }
        return false;
    }

    private function isAriaHidden($attributes): bool
    {
        return (array_key_exists('aria-hidden', $attributes) &&
            ($attributes['aria-hidden'] === true || $attributes['aria-hidden'] === 'true'));
    }

    private function generateMaskString(array $otherMasks): string
    {
        $mathRandom = ObjectManager::getInstance()->get(MathRandom::class);
        do {
            $mask = $mathRandom->getRandomString(32, $mathRandom::CHARS_LOWERS);
        } while (isset($otherMasks[$mask]));

        return $mask;
    }

    private function withMaskedAlpineAttributes(string $content, callable $fn): string
    {
        $maskedAttributes = [];

        while (preg_match('/<[a-zA-Z][^>]+?\s([@:][a-z][^=]*)=/', $content, $matches)) {
            $mask = $this->generateMaskString($maskedAttributes);
            $maskedAttributes[$mask] = $matches[1];
            $content = str_replace($matches[1], $mask, $content);
        }

        $content = $fn($content);

        return str_replace(array_keys($maskedAttributes), array_values($maskedAttributes), $content);
    }

    private function getCacheKey(string $iconPath, string $classNames, ?int $width, ?int $height, array $attributes): string
    {
        return $this->design->getDesignTheme()->getCode() .
            '/' . $iconPath .
            '/' . $classNames .
            '#' . $width .
            '#' . $height .
            '#' . hash('md5', json_encode($attributes));
    }
}
