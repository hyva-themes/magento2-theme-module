<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Bundle;

use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Tax\Helper\Data as TaxHelper;

class HideZeroFixedBundleSelectionPricePlugin
{
    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var TaxHelper
     */
    private $taxHelper;

    public function __construct(
        PricingHelper $pricingHelper,
        TaxHelper $taxHelper
    ) {
        $this->pricingHelper = $pricingHelper;
        $this->taxHelper = $taxHelper;
    }

    /**
     * Omit a zero selection price on fixed-price bundles, and show non-zero ones as an addition.
     *
     * Magento always appends the formatted price in Configuration::getOptionPriceHtml(), which is
     * private and cannot be plugged. A zero on a fixed-price bundle means "included in the bundle
     * price", not "free". Dynamic-price bundles are left unchanged: there the selection price is
     * the child's own price.
     *
     * @param Configuration $subject
     * @param array $options
     * @param ItemInterface $item
     * @return array
     * @see Configuration::getBundleOptions
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetBundleOptions(Configuration $subject, array $options, ItemInterface $item): array
    {
        // Non-strict on purpose: the constant is an int, the product attribute is a string
        if ($item->getProduct()->getPriceType() != Price::PRICE_TYPE_FIXED) {
            return $options;
        }

        foreach ($options as $optionIndex => $option) {
            if (empty($option['value']) || !is_array($option['value'])) {
                continue;
            }
            foreach ($option['value'] as $valueIndex => $value) {
                if (!is_string($value)) {
                    continue;
                }
                $options[$optionIndex]['value'][$valueIndex] = $this->adjustSelectionValue($value);
            }
        }

        return $options;
    }

    private function adjustSelectionValue(string $value): string
    {
        $zeroPriceSuffix = $this->getZeroPriceSuffix();
        if (str_ends_with($value, $zeroPriceSuffix)) {
            return substr($value, 0, -strlen($zeroPriceSuffix));
        }

        return $this->prefixNonZeroPriceAsAddition($value);
    }

    /**
     * Build the same trailing price HTML Magento appends for a zero selection.
     *
     * @see Configuration::getOptionPriceHtml
     */
    private function getZeroPriceSuffix(): string
    {
        $formattedZero = (string) $this->pricingHelper->currency(0);
        $suffix = ' ' . $formattedZero;
        if ($this->taxHelper->displayCartBothPrices()) {
            $suffix .= ' ' . __('Excl. tax:') . ' ' . $formattedZero;
        }

        return $suffix;
    }

    private function prefixNonZeroPriceAsAddition(string $value): string
    {
        $priceContainer = '<span class="price">';
        $pos = strpos($value, $priceContainer);
        if ($pos === false) {
            return $value;
        }

        return substr($value, 0, $pos) . '+ ' . substr($value, $pos);
    }
}
