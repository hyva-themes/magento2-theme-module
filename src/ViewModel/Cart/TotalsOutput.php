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

class TotalsOutput implements ArgumentInterface
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function getSubtotalField()
    {
        return $this->config->displayCartSubtotalExclTax() ? 'subtotal_excluding_tax' : 'subtotal_including_tax';
    }

    public function getSubtotalFieldDisplayBoth()
    {
        return $this->config->displayCartSubtotalBoth() ? 'subtotal_excluding_tax' : false;
    }

    public function getTaxLabelAddition()
    {
        return $this->config->displayCartSubtotalExclTax() ?
            __('excl.') :
            (
            $this->config->displayCartSubtotalBoth() ?
                '' :
                __('incl.')
            );
    }

    public function getShippingLabelAddition()
    {
        return !$this->config->shippingPriceIncludesTax() ? __('excl.') . ' ' : '';
    }

}
