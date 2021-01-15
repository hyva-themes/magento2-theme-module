<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Cart;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Tax\Model\Config;

class ItemOutput implements ArgumentInterface
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function isItemPriceDisplayBoth()
    {
        return $this->config->displayCartPricesBoth();
    }

    public function isItemPriceInclTax()
    {
        return $this->config->displayCartPricesInclTax();
    }

    public function isItemPriceExclTax()
    {
        return $this->config->displayCartPricesExclTax();
    }
}
