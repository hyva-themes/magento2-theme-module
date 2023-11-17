<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block;

use Magento\Framework\View\Element\AbstractBlock;

class SortableItems extends AbstractBlock
{
    protected function _toHtml(): string
    {
        $sortedItems = $this->getSortedItems();
        return empty($sortedItems) ? '' : implode('', $sortedItems);
    }

    /**
     * @return string[]
     */
    private function getSortedItems(): array
    {
        $childBlocks = $this->_layout->getChildBlocks($this->getNameInLayout());
        $sortedItems = [];
        foreach ($childBlocks as $childBlock) {
            if ($childBlock instanceof SortableItemInterface === false || $childBlock->getSortOrder() === null) {
                $childBlock->setData(
                    SortableItemInterface::SORT_ORDER,
                    SortableItemInterface::SORT_ORDER_DEFAULT_VALUE
                );
            }
            $sortedItems[$childBlock->getSortOrder()] = $childBlock->toHtml();
        }

        ksort($sortedItems);
        return $sortedItems;
    }
}
