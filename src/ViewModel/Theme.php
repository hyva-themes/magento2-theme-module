<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Theme implements ArgumentInterface
{
    /**
     * @var DesignInterface
     */
    private $viewDesign;

    public function __construct(DesignInterface $viewDesign)
    {
        $this->viewDesign = $viewDesign;
    }

    public function isHyva(): bool
    {
        $theme = $this->viewDesign->getDesignTheme();
        while ($theme) {
            if (strpos($theme->getCode(), 'Hyva/') === 0) {
                return true;
            }
            $theme = $theme->getParentTheme();
        }
        return false;
    }
}
