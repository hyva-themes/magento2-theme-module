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

class SortableLinks extends AbstractBlock
{
    protected function _toHtml(): string
    {
        $linkBlocks = $this->getLinkBlocks();
        return empty($linkBlocks) ? '' : implode('', $linkBlocks);
    }

    private function getLinkBlocks(): array
    {
        $linkBlocks = $this->_layout->getChildBlocks($this->getNameInLayout());
        $sortableLinks = [];
        foreach ($linkBlocks as $linkBlock) {
            if ($linkBlock instanceof SortableLinkInterface === false || $linkBlock->getSortOrder() === null) {
                $linkBlock->setData(
                    SortableLinkInterface::SORT_ORDER,
                    SortableLinkInterface::SORT_ORDER_DEFAULT_VALUE
                );
            }
            $sortableLinks[$linkBlock->getSortOrder()] = $linkBlock->toHtml();
        }

        ksort($sortableLinks);
        return $sortableLinks;
    }
}
