<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
