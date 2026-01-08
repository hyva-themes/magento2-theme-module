<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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

    public function getSubtotalField(): string
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
            ($this->taxConfig->displayCartSubtotalBoth() ? '' : __('incl.'));
    }

    public function getShippingLabelAddition(): string
    {
        return !$this->taxConfig->shippingPriceIncludesTax() ? __('excl.') . ' ' : '';
    }

    public function displayCartTaxWithGrandTotal(): bool
    {
        return $this->taxConfig->displayCartTaxWithGrandTotal();
    }

    public function displayCartShippingExclTax(): bool
    {
        return $this->taxConfig->displayCartShippingExclTax();
    }

    public function displayCartShippingInclTax(): bool
    {
        return $this->taxConfig->displayCartShippingInclTax();
    }

    public function displayCartShippingBoth(): bool
    {
        return $this->taxConfig->displayCartShippingBoth();
    }
}
