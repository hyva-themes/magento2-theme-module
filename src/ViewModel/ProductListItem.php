<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;

class ProductListItem implements ArgumentInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    public function getProductPriceHtml(
        Product $product
    ) {
        $priceType = FinalPrice::PRICE_CODE;

        $arguments = [
            'include_container' => true,
            'display_minimal_price' => true,
            'list_category_page' => true,
            'zone' => Render::ZONE_ITEM_LIST
        ];

        /** @var Render $priceRender */
        $priceRender = $this->layout->getBlock('product.price.render.default');
        $price = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }
}
