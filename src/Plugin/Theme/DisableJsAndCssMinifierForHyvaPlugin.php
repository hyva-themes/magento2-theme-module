<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Magento\Framework\App\Area;
use Magento\Framework\View\Asset\PreProcessor\Chain as PreProcessorChain;
use Magento\Framework\View\Asset\PreProcessor\Minify as MinifyPreProcessor;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;

class DisableJsAndCssMinifierForHyvaPlugin
{
    private $memoizedHyvaThemes = [];

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    public function __construct(
        ThemeProviderInterface $themeProvider
    ) {
        $this->themeProvider = $themeProvider;
    }

    public function aroundProcess(MinifyPreProcessor $subject, callable $proceed, PreProcessorChain $chain): void
    {
        $themePath = implode('/', array_slice(explode('/', $chain->getTargetAssetPath()), 0, 3));
        if ($this->isHyva($themePath)) {
            return;
        }
        $proceed($chain);
    }

    public function isHyva(string $themePath): bool
    {
        if (!isset($this->memoizedHyvaThemes[$themePath])) {
            $this->memoizedHyvaThemes[$themePath] = $this->determineIfHyvaTheme($themePath);
        }
        return $this->memoizedHyvaThemes[$themePath];
    }

    private function determineIfHyvaTheme(string $themePath): bool
    {
        $theme = $this->themeProvider->getThemeByFullPath($themePath);
        while ($theme && $theme->getArea() === Area::AREA_FRONTEND) {
            if (strpos($theme->getCode(), 'Hyva/') === 0) {
                return true;
            }
            $theme = $theme->getParentTheme();
        }
        return false;
    }
}
