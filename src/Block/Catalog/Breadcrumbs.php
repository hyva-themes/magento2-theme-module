<?php

namespace Hyva\Theme\Block\Catalog;

use Magento\Framework\View\Element\Template;

class Breadcrumbs extends Template

{
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
        return $this;
    }
}
