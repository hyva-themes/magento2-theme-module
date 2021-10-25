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

/**
 * Deprecated because using string templates breaks the non-js GraphQL query parser used by GraphQLEditor.
 *
 * The replacement class \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables returns queries and
 * mutations utilizing GraphQL variables instead of using JavaScript string templates.
 * This has the benefit of being GraphQL parser compliant, and thus will work well with the GraphqlQueryEditor.
 *
 * @deprecated
 * @see \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables
 */
class GraphQlQueries implements ArgumentInterface
{
    /**
     * @var GraphQlQueriesWithVariables
     */
    private $graphQlQueriesWithVariables;

    public function __construct(GraphQlQueriesWithVariables $graphQlQueriesWithVariables)
    {
        $this->graphQlQueriesWithVariables = $graphQlQueriesWithVariables;
    }

    public function getCartGraphQlQuery()
    {
        return $this->graphQlQueriesWithVariables->getCartGraphQlQuery();
    }

    /**
     * Deprecated because using string templates breaks the non-js GraphQL query parser used by GraphQLEditor.
     *
     * @see \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables::getCouponAddQuery()
     */
    public function getCouponAddQuery()
    {
        return 'applyCouponToCart (
                    input: {
                      cart_id: "${this.cartId}",
                      coupon_code: "${couponCode}",
                    }
                ){
                    cart {
                        ' . $this->getCartGraphQlQuery() . '
                    }
                }';
    }

    /**
     * Deprecated because using string templates breaks the non-js GraphQL query parser used by GraphQLEditor.
     *
     * @see \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables::getCouponRemoveQuery()
     */
    public function getCouponRemoveQuery()
    {
        return 'removeCouponFromCart(
                    input: {
                      cart_id: "${this.cartId}"
                    }
                  ) {
                    cart {
                        ' . $this->getCartGraphQlQuery() . '
                    }
                 }';
    }

    /**
     * Deprecated because using string templates breaks the non-js GraphQL query parser used by GraphQLEditor.
     *
     * @see \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables::getCartItemUpdateQuery()
     */
    public function getCartItemUpdateQuery()
    {
        return 'updateCartItems(
                 input: {
                  cart_id: "${this.cartId}",
                  cart_items: [
                    {
                      cart_item_id: ${itemId}
                      quantity: ${qty}
                    }
                  ]
                }
              ) {
                cart {
                    ' . $this->getCartGraphQlQuery() . '
                }
             }';
    }

    /**
     * Deprecated because using string templates breaks the non-js GraphQL query parser used by GraphQLEditor.
     *
     * @see \Hyva\Theme\ViewModel\Cart\GraphQlQueriesWithVariables::getCartItemRemoveQuery()
     */
    public function getCartItemRemoveQuery()
    {
        return 'removeItemFromCart(
                input: {
                    cart_id: $cartId,
                    cart_item_id: $itemId
                  }
                ){
                cart {
                    ' . $this->getCartGraphQlQuery() . '
                }
             }';
    }
}
