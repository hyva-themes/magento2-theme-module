<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Block;

use Magento\Framework\View\Element\Template;

class SortableItem extends Template implements SortableItemInterface
{
    public function getLabel(): string
    {
        return $this->getData(self::LABEL) ?? '';
    }

    public function getPath(): string
    {
        return $this->getData(self::PATH) ?? '';
    }

    public function getSortOrder(): ?int
    {
        $sortOrder = $this->getData(self::SORT_ORDER);
        return $sortOrder ? (int) $sortOrder : null;
    }

    protected function _prepareLayout(): self
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(self::TEMPLATE);
        }
        return $this;
    }
}
