<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Catalog;

use Magento\Framework\View\Element\Template;

class Breadcrumbs extends Template
{
    protected function _prepareLayout(): Template
    {
        parent::_prepareLayout();
        $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
        return $this;
    }
}
