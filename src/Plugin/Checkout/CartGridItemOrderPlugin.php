<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Checkout;

use Hyva\Theme\Service\CurrentTheme;
use Hyva\Theme\ViewModel\Cart\ItemOrder;
use Magento\Checkout\Block\Cart\Grid;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Quote\Model\ResourceModel\Quote\Item\Collection as QuoteItemCollection;

/**
 * Show the newest cart items first on the cart page if "Show Newest Items First" is enabled.
 *
 * The paginated collection is sorted before it is loaded, so the newest items are on the first page.
 */
class CartGridItemOrderPlugin
{
    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var ItemOrder
     */
    private $itemOrder;

    public function __construct(CurrentTheme $currentTheme, ItemOrder $itemOrder)
    {
        $this->currentTheme = $currentTheme;
        $this->itemOrder = $itemOrder;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItemsForGrid(Grid $subject, QuoteItemCollection $collection): QuoteItemCollection
    {
        if (! $collection->isLoaded() && $this->isNewestItemsFirst()) {
            $collection->setOrder('item_id', QuoteItemCollection::SORT_ORDER_DESC);
        }

        return $collection;
    }

    /**
     * @param Grid $subject
     * @param QuoteItem[] $items
     * @return QuoteItem[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetItems(Grid $subject, array $items): array
    {
        if (! $this->isNewestItemsFirst()) {
            return $items;
        }

        usort($items, function (QuoteItem $itemA, QuoteItem $itemB): int {
            return (int) $itemB->getId() <=> (int) $itemA->getId();
        });

        return $items;
    }

    private function isNewestItemsFirst(): bool
    {
        return $this->currentTheme->isHyva() && $this->itemOrder->isShowNewestItemsFirst();
    }
}
