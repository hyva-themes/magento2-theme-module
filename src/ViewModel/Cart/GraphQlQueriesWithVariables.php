<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Cart;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class GraphQlQueriesWithVariables implements ArgumentInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $magento;

    public function __construct(ProductMetadataInterface $magento)
    {
        $this->magento = $magento;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.LongVariable)
     */
    public function getCartGraphQlQuery()
    {
        $configuredVariantImageQuery = version_compare($this->magento->getVersion(), '2.4.3', '>=')
            ? 'configured_variant {
                    small_image {
                      label
                      url
                    }
                  }'
            : '';

        $errors = version_compare($this->magento->getVersion(), '2.4.5', '>=')
            ? 'errors {
                  code
                  message
                }'
            : 'errors';

        return "
              total_quantity
              is_virtual
              items {
                id
                {$errors}
                prices {
                  price {
                    value
                  }
                  row_total {
                    value
                    currency
                  }
                  row_total_incl_tax {
                    value
                    currency
                  }
                  price_incl_tax{
                    value
                  }
                }
                product_type
                product {
                  id
                  name
                  sku
                  small_image {
                    label
                    url
                  }
                  url_key
                  url_suffix
                  price_tiers {
                      quantity
                      final_price {
                        value
                      }
                      discount {
                        amount_off
                        percent_off
                      }
                  }
                }
                quantity
                ... on SimpleCartItem {
                  customizable_options {
                    label
                      values {
                        label
                        value
                        price {
                        value
                        type
                      }
                    }
                  }
                }
                ... on VirtualCartItem {
                  customizable_options {
                    label
                      values {
                        label
                        value
                        price {
                        value
                        type
                      }
                    }
                  }
                }
                ... on DownloadableCartItem {
                  customizable_options {
                    label
                      values {
                        label
                        value
                        price {
                        value
                        type
                      }
                    }
                  }
                }

                ... on ConfigurableCartItem {
                  configurable_options {
                    id
                    option_label
                    value_label
                  }
                  {$configuredVariantImageQuery}
                }
                ... on BundleCartItem {
                  bundle_options {
                    id
                    label
                    values {
                      quantity
                      label
                    }
                  }
                  customizable_options {
                    label
                      values {
                        label
                        value
                        price {
                        value
                        type
                      }
                    }
                  }
                }
              }
              available_payment_methods {
                code
                title
              }
              selected_payment_method {
                code
                title
              }
              applied_coupons {
                code
              }
              billing_address {
                country {
                  code
                }
                region {
                  label
                  region_id
                }
                postcode
              }
              shipping_addresses {
                country {
                  code
                }
                region {
                  label
                  region_id
                }
                postcode
                selected_shipping_method {
                  amount {
                    value
                    currency
                  }
                  carrier_title
                  carrier_code
                  method_title
                  method_code
                }
                available_shipping_methods {
                  price_excl_tax {
                    value
                    currency
                  }
                  price_incl_tax {
                    value
                    currency
                  }
                  available
                  carrier_title
                  carrier_code
                  error_message
                  method_title
                  method_code
                }
              }
              prices {
                grand_total {
                  value
                  currency
                }
                subtotal_excluding_tax {
                  value
                  currency
                }
                subtotal_including_tax {
                  value
                  currency
                }
                applied_taxes {
                  amount {
                      value
                      currency
                  }
                  label
                }
                discounts {
                  amount {
                      value
                      currency
                  }
                  label
                }
              }
          ";
    }

    public function getCouponAddQuery(): string
    {
        return 'applyCouponToCart (
                    input: {
                      cart_id: $cartId,
                      coupon_code: $couponCode
                    }
                ){
                    cart {
                        ' . $this->getCartGraphQlQuery() . '
                    }
                }';
    }

    /**
     * Return the inner part of the query to remove a coupon code from the cart.
     *
     * This method replaces the deprecated getCouponRemoveQuery() method below.
     */
    public function getCouponRemoveQuery(): string
    {
        return 'removeCouponFromCart(
                    input: {
                      cart_id: $cartId
                    }
                  ) {
                    cart {
                        ' . $this->getCartGraphQlQuery() . '
                    }
                 }';
    }

    /**
     * Return the inner part of the GraphQL query to update an item quantity.
     *
     * This method replaces the deprecated method getCartItemUpdateQuery() below.
     */
    public function getCartItemUpdateQuery(): string
    {
        return 'updateCartItems(
                 input: {
                  cart_id: $cartId,
                  cart_items: [
                    {
                      cart_item_id: $itemId
                      quantity: $qty
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
     * Return the inner part of the GraphQL query to remove an item from the cart.
     *
     * This method replaces the deprecated method getCartItemRemoveQuery() below.
     */
    public function getCartItemRemoveQuery(): string
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
