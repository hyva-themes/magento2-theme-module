<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Hyva\Theme\Service\HyvaThemes;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Asset\PreProcessor\Chain as PreProcessorChain;
use Magento\Framework\View\Asset\PreProcessor\Minify as MinifyPreProcessor;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class DisableJsAndCssMinifierForHyvaPlugin
{
    /**
     * @var HyvaThemes
     */
    private $hyvaThemes;

    public function __construct(
        ThemeProviderInterface $themeProvider, // keep to preserve BC
        ?HyvaThemes $hyvaThemes = null
    ) {
        $this->hyvaThemes = $hyvaThemes ?? ObjectManager::getInstance()->get(HyvaThemes::class);
    }

    public function aroundProcess(MinifyPreProcessor $subject, callable $proceed, PreProcessorChain $chain): void
    {
        $themePath = implode('/', array_slice(explode('/', $chain->getTargetAssetPath()), 0, 3));
        if ($this->hyvaThemes->isHyvaThemeCode($themePath)) {
            return;
        }
        $proceed($chain);
    }
}
