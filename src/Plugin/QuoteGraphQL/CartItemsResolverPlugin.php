<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\QuoteGraphQL;

use Magento\Catalog\Model\Product;
use Magento\QuoteGraphQl\Model\Resolver\CartItems;

class CartItemsResolverPlugin
{
    /**
     * @param CartItems $subject
     * @param array $itemsData
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterResolve(
        CartItems $subject,
        $itemsData
    ) {
        foreach ($itemsData as $index => $cartItem) {
            /**
             * This cartItem is likely an error message
             */
            if (!is_array($cartItem)) {
                continue;
            }

            /**
             * Add errors to the quoteItem
             */
            $quoteItem = $cartItem['model'];
            if ($quoteItem->getHasError() && $quoteItem->getMessage()) {
                $itemsData[$index]['errors'] = $quoteItem->getMessage();
            }

            /**
             * If `product_type` is set as quote_item_option we're dealing
             * with a Grouped product and want to set the url_key to the
             * grouped product's value
             */
            $option = $quoteItem->getOptionByCode('product_type');
            if ($option) {
                $cartProduct = $cartItem['product'];
                /** @var Product $product */
                $product = $option->getProduct();
                $cartProduct['url_key'] = $product->getUrlKey();
                $itemsData[$index]['product'] = $cartProduct;
            }
        }
        return $itemsData;
    }
}
