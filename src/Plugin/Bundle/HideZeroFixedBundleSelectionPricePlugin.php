<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Bundle;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Bundle\Helper\Catalog\Product\Configuration;
use Magento\Bundle\Model\Product\Price;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Tax\Helper\Data as TaxHelper;

class HideZeroFixedBundleSelectionPricePlugin
{
    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * @var TaxHelper
     */
    private $taxHelper;

    public function __construct(
        CurrentTheme $currentTheme,
        PricingHelper $pricingHelper,
        TaxHelper $taxHelper
    ) {
        $this->currentTheme = $currentTheme;
        $this->pricingHelper = $pricingHelper;
        $this->taxHelper = $taxHelper;
    }

    /**
     * Omit a zero selection price on fixed-price bundles, and show non-zero ones as an addition.
     *
     * Magento appends the formatted price to each selection value in getBundleOptions(). Since
     * Magento 2.4.7 this happens in the private Configuration::getOptionPriceHtml(), which also
     * appends the price excluding tax if the cart displays both prices. Before 2.4.7 only the
     * single price is appended, regardless of the tax display setting.
     *
     * A zero on a fixed-price bundle means "included in the bundle price", not "free".
     * Dynamic-price bundles are left unchanged: there the selection price is the child's own price.
     *
     * Only applies to Hyvä themes, so non-Hyvä store views and pages rendered with a non-Hyvä theme,
     * like the Luma checkout fallback, keep the Magento default output.
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
        if (! $this->currentTheme->isHyva()) {
            return $options;
        }

        // Non-strict on purpose: the constant is an int, the product attribute is a string
        if ($item->getProduct()->getPriceType() != Price::PRICE_TYPE_FIXED) {
            return $options;
        }

        $zeroPriceSuffixes = $this->getZeroPriceSuffixes();
        foreach ($options as $optionIndex => $option) {
            if (empty($option['value']) || !is_array($option['value'])) {
                continue;
            }
            foreach ($option['value'] as $valueIndex => $value) {
                if (!is_string($value)) {
                    continue;
                }
                $options[$optionIndex]['value'][$valueIndex] = $this->adjustSelectionValue($value, $zeroPriceSuffixes);
            }
        }

        return $options;
    }

    private function adjustSelectionValue(string $value, array $zeroPriceSuffixes): string
    {
        foreach ($zeroPriceSuffixes as $zeroPriceSuffix) {
            if (str_ends_with($value, $zeroPriceSuffix)) {
                return substr($value, 0, -strlen($zeroPriceSuffix));
            }
        }

        return $this->prefixNonZeroPriceAsAddition($value);
    }

    /**
     * Build the trailing price HTML Magento appends for a zero selection, longest first.
     *
     * If the cart displays both prices, Magento 2.4.7 and newer append "<incl> Excl. tax: <excl>",
     * while older versions append only one price. The longer suffix has to be checked first,
     * because it also ends with the shorter one.
     *
     * @return string[]
     */
    private function getZeroPriceSuffixes(): array
    {
        // Converting zero to the display currency is still zero, so this matches the core output
        $zeroPriceSuffix = ' ' . $this->pricingHelper->currency(0);
        if (!$this->taxHelper->displayCartBothPrices()) {
            return [$zeroPriceSuffix];
        }

        return [$zeroPriceSuffix . ' ' . __('Excl. tax:') . $zeroPriceSuffix, $zeroPriceSuffix];
    }

    private function prefixNonZeroPriceAsAddition(string $value): string
    {
        $priceContainer = '<span class="price">';

        // Prefix every price, so both the price including and excluding tax are shown as additions
        return str_replace($priceContainer, '+ ' . $priceContainer, $value);
    }
}
