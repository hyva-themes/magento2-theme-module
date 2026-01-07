<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\ConfigInterface as ViewConfig;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PageBuilder implements ArgumentInterface
{
    /**
     * @var ViewConfig
     */
    private $viewConfig;

    public function __construct(
        ViewConfig $viewConfig
    ) {
        $this->viewConfig = $viewConfig;
    }

    public function getMobileMediaQuery(): string
    {
        return (string) $this->getMediaQuery('mobile', '768px');
    }

    /**
     * Return the media query based on the theme's view.xml file, or the specified default breakpoint.
     *
     * @see \Magento\PageBuilder\Model\Filter\Template::getMediaQuery
     * @param string $view
     * @return string|null
     */
    private function getMediaQuery(string $view, ?string $defaultBreakpoint = null): ?string
    {
        $breakpoint = $this->viewConfig->getViewConfig()->getVarValue(
            'Magento_PageBuilder',
            'breakpoints/' . $view . '/conditions/max-width'
        ) ?? $defaultBreakpoint ?? null;

        return $breakpoint ? "@media only screen and (max-width: {$breakpoint})" : null;
    }
}
