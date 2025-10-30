<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Service;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\DesignInterface;

/**
 * Provides information about the current theme
 */
class CurrentTheme
{
    /**
     * @var DesignInterface
     */
    protected $viewDesign;

    /**
     * @var HyvaThemes
     */
    private $hyvaThemes;

    public function __construct(
        DesignInterface $viewDesign,
        ?HyvaThemes $hyvaThemes = null
    ) {
        $this->viewDesign = $viewDesign;
        $this->hyvaThemes = $hyvaThemes ?? ObjectManager::getInstance()->get(HyvaThemes::class);
    }

    /**
     * Returns true if the current theme is a Hyva theme
     *
     * @return bool
     */
    public function isHyva(): bool
    {
        $theme = $this->viewDesign->getDesignTheme();
        return $this->hyvaThemes->isHyvaTheme($theme);
    }
}
