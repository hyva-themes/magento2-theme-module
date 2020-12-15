<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\Quote\Item;
use Magento\QuoteGraphQl\Model\Resolver\CartItemPrices as QuoteCartItemPrices;

class CartItemPrices extends QuoteCartItemPrices implements ResolverInterface
{
    /**
     * Add quote item price including tax
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws \Exception
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $return = parent::resolve($field, $context, $info, $value, $args);

        /** @var Item $cartItem */
        $cartItem = $value['model'];
        $currencyCode = $cartItem->getQuote()->getQuoteCurrencyCode();

        return $return + [
            'price_incl_tax' => [
                'currency' => $currencyCode,
                'value' => $cartItem->getPriceInclTax(),
            ],
            'row_total_incl_tax' => [
                'currency' => $currencyCode,
                'value' => $cartItem->getRowTotalInclTax(),
            ]
        ];
    }
}
