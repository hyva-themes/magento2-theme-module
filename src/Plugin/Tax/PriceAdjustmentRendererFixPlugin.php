<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Tax;

use Magento\Framework\Pricing\Render\AmountRenderInterface;
use Magento\Tax\Pricing\Render\Adjustment;

class PriceAdjustmentRendererFixPlugin
{
    /**
     * @var AmountRenderInterface
     */
    private $amountRender;

    /**
     * Store amountRender for use in getDataPriceType around plugin method below.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeRender(Adjustment $subject, AmountRenderInterface $amountRender)
    {
        $this->amountRender = $amountRender;
        return null;
    }

    /**
     * Work around PHP 8.1 type error core bug.
     *
     * Issue https://github.com/magento/magento2/issues/35500 is closed and fix is scheduled to be delivered in 2.4.6
     * This fix applies to both Hyvä and Luma in older versions of Magento.
     *
     * This method intentionally does not delegate to the parent method.
     */
    public function aroundGetDataPriceType(): string
    {
        // The core bug was passing null to ucfirst() if price_type was not set.
        $priceType = $this->amountRender->getPriceType();
        return $priceType === 'finalPrice' ? 'basePrice' : ($priceType ? 'base' . ucfirst($priceType) : '');
    }
}
