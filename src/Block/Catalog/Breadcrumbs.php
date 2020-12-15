<?php

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
