<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\View\Asset;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * This generic SvgIcons view model can be used to render any icon set (i.e. subdirectory in web/svg).
 *
 * The icon set can be configured with di.xml or by extending the class. The module ships with Heroicons
 * and two preconfigured view models:
 *
 * @see HeroiconsSolid
 * @see HeroiconsOutline
 */
class SvgIcons implements ArgumentInterface
{
    private const CACHE_TAG = 'HYVA_ICONS';

    public const HEROICONS_OUTLINE = 'heroicons/outline';
    public const HEROICONS_SOLID   = 'heroicons/solid';

    /**
     * @var string Path relative to asset directory Hyva_Theme::svg/
     */
    private $iconSet;

    /**
     * @var Asset\Repository
     */
    private $assetRepository;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var array<string,string>
     */
    private $svgCache = [];

    public function __construct(
        Asset\Repository $assetRepository,
        CacheInterface $cache,
        string $iconSet = self::HEROICONS_OUTLINE
    ) {
        $this->iconSet = $iconSet;
        $this->assetRepository = $assetRepository;
        $this->cache = $cache;
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
     * @return string
     */
    public function renderHtml(string $icon, string $classNames = '', ?int $width = null, ?int $height = null): string
    {
        $cacheKey = $icon . '/' . $classNames . '#' . $width . '#' . $height;
        if ($result = $this->cache->load($cacheKey)) {
            return $result;
        }
        $svg = \file_get_contents($this->getFilePath($icon));
        $svgXml = new \SimpleXMLElement($svg);
        if (trim($classNames)) {
            $svgXml->addAttribute('class', $classNames);
        }
        if ($width) {
            $svgXml->addAttribute('width', (string) $width);
        }
        if ($height) {
            $svgXml->addAttribute('height', (string) $height);
        }
        $result = \str_replace("<?xml version=\"1.0\"?>\n", '', $svgXml->asXML());
        $this->cache->save($result, $cacheKey, [self::CACHE_TAG]);
        return $result;
    }

    /**
     * Magic method to allow iconNameHtml() instead of renderHtml('icon-name'). Subclasses may
     * use @method doc blocks to provide autocompletion for available icons.
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
     */
    private static function camelCaseToKebabCase(string $in): string
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1-$2", $in));
    }

    /**
     * Return full path to icon file, respecting theme fallback
     */
    private function getFilePath(string $icon): string
    {
        $assetFileId = 'Hyva_Theme::svg/' . $this->iconSet . '/' . $icon . '.svg';
        return $this->assetRepository->createAsset($assetFileId)->getSourceFile();
    }
}
