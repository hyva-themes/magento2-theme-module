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
use Magento\Tax\Model\Config as TaxConfig;

class TotalsOutput implements ArgumentInterface
{
    /**
     * @var TaxConfig
     */
    protected $taxConfig;

    public function __construct(TaxConfig $config)
    {
        $this->taxConfig = $config;
    }

    public function getSubtotalField()
    {
        return $this->taxConfig->displayCartSubtotalExclTax() ? 'subtotal_excluding_tax' : 'subtotal_including_tax';
    }

    public function getSubtotalFieldDisplayBoth()
    {
        return $this->taxConfig->displayCartSubtotalBoth() ? 'subtotal_excluding_tax' : false;
    }

    public function getTaxLabelAddition()
    {
        return $this->taxConfig->displayCartSubtotalExclTax() ?
            __('excl.') :
            (
            $this->taxConfig->displayCartSubtotalBoth() ?
                '' :
                __('incl.')
            );
    }

    public function getShippingLabelAddition()
    {
        return !$this->taxConfig->shippingPriceIncludesTax() ? __('excl.') . ' ' : '';
    }

    public function displayCartTaxWithGrandTotal(): bool
    {
        return $this->taxConfig->displayCartTaxWithGrandTotal();
    }
}
