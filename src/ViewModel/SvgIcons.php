<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Asset;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SvgIcons implements ArgumentInterface
{
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

    public function __construct(Asset\Repository $assetRepository, string $iconSet = self::HEROICONS_OUTLINE)
    {
        $this->iconSet = $iconSet;
        $this->assetRepository = $assetRepository;
    }

    /**
     * Renders an inline SVG icon from the configured icon set
     *
     * The method ends with Html instead of Svg so that the Magento code sniffer understands it is safe HTML and does
     * not need to be escaped.
     *
     * @param string $icon The SVG file name without .svg suffix
     * @param string $classNames CSS classes to add to the root element, space separated
     * @return string
     */
    public function renderHtml(string $icon, string $classNames = ''): string
    {
        //TODO sanitize SVGs as in integer-net/magento2-storegraphics
        //TODO add CSS classes
        return \file_get_contents($this->getFilePath($icon));
    }

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

    private function getFilePath(string $icon)
    {
        $assetFileId = 'Hyva_Theme::svg/' . $this->iconSet . '/' . $icon . '.svg';
        return $this->assetRepository->createAsset($assetFileId)->getSourceFile();
    }
}
