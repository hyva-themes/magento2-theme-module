<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Block;

use Magento\Framework\View\Element\AbstractBlock;
use function array_map as map;

class SortableItems extends AbstractBlock
{
    protected function _toHtml(): string
    {
        return implode('', map(function ($block): string {
            return $block->toHtml();
        }, $this->getSortedItems()));
    }

    /**
     * @return string[]
     */
    private function getSortedItems(): array
    {
        $childBlocks = $this->_layout->getChildBlocks($this->getNameInLayout());
        foreach ($childBlocks as $childBlock) {
            if ($childBlock instanceof SortableItemInterface === false && $childBlock->getSortOrder() === null) {
                $childBlock->setData(
                    SortableItemInterface::SORT_ORDER,
                    SortableItemInterface::SORT_ORDER_DEFAULT_VALUE
                );
            }

        }

        usort($childBlocks, static function ($a, $b) {
            return ((int) $a->getSortOrder()) <=> ((int) $b->getSortOrder());
        });

        return $childBlocks;
    }
}
