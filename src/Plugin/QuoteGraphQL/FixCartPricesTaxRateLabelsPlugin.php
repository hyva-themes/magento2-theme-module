<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\QuoteGraphQL;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Resolver\CartPrices;
use function array_values as vals;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged

class FixCartPricesTaxRateLabelsPlugin
{
    /**
     * Fix resolver tax rate labels
     *
     * The original resolver returns the admin scope rate IDs, not the label appropriate for the store context.
     * This plugin replaces the applied tax rate labels with the correct labels for the requested store.
     *
     * @param CartPrices $subject
     * @param array $result
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterResolve(
        CartPrices $subject,
        array $result,
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ) {
        /** @var Quote $quote */
        $quote   = $value['model'];
        $address = $quote->isVirtual() ? $quote->getBillingAddress() : $quote->getShippingAddress();

        // The items applied taxes contains the store specific labels
        $taxRows           = $address->getData('items_applied_taxes') ?: [];
        $itemsAppliedTaxes = $taxRows ? call_user_func_array('array_merge', vals($taxRows)) : [];
        foreach (($result['applied_taxes'] ?? []) as $i => $rate) {
            $result['applied_taxes'][$i]['label'] = $this->findStoreTaxLabelByCode($rate['label'], $itemsAppliedTaxes);
        }

        return $result;
    }

    /**
     * Return the given rate title if present in applied taxes array
     *
     * The method has no retur type and no type on $rateCode on purpose for taxjar/module-taxjar compatibility.
     * See https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/146 for details.
     *
     * @param string $rateCode
     * @param array $itemsAppliedTaxes
     * @return string
     */
    private function findStoreTaxLabelByCode($rateCode, array $itemsAppliedTaxes)
    {
        foreach ($itemsAppliedTaxes as $appliedTax) {
            if ($appliedTax['id'] === $rateCode) {
                return $appliedTax['rates'][0]['title'] ?? $rateCode;
            }
        }
        return $rateCode;
    }
}
