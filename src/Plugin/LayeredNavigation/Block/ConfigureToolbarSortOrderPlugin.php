<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\LayeredNavigation\Block;

use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\LayeredNavigation\Block\Navigation;

class ConfigureToolbarSortOrderPlugin
{
    private const PRODUCT_LISTING_TOOLBAR_BLOCK = 'product_list_toolbar';

    /**
     * Pass the sort options of the product list on to the toolbar before the layered navigation renders.
     *
     * Navigation hands the collection to the shared product_list_toolbar block while it renders, and
     * Toolbar::setCollection() memoizes the current order and direction in the process. Hyvä puts the
     * sidebar before the main column, so that happens before ListProduct::configureToolbar() passes on
     * the options the page prepared, and the toolbar is stuck with the store defaults. On the search
     * page those defaults are position/asc, which is not even an available order there, leaving the
     * sorter without a selected option and the direction toggle unable to change anything.
     *
     * @param Navigation $subject
     * @return void
     */
    public function beforeToHtml(Navigation $subject): void
    {
        $layout = $subject->getLayout();

        $toolbar = $layout->getBlock(self::PRODUCT_LISTING_TOOLBAR_BLOCK);
        if (! $toolbar instanceof Toolbar) {
            return;
        }

        $parentName = $layout->getParentName(self::PRODUCT_LISTING_TOOLBAR_BLOCK);
        $productList = $parentName ? $layout->getBlock($parentName) : false;
        if (! $productList instanceof ListProduct) {
            return;
        }

        $availableOrders = $productList->getAvailableOrders();
        if ($availableOrders) {
            $toolbar->setAvailableOrders($availableOrders);
        }

        // setDefaultOrder only accepts a field that is in the available orders, so it has to come second
        $order = $productList->getSortBy() ?: $productList->getDefaultSortBy();
        if ($order) {
            $toolbar->setDefaultOrder($order);
        }

        $direction = $productList->getDefaultDirection();
        if ($direction) {
            $toolbar->setDefaultDirection($direction);
        }
    }
}
