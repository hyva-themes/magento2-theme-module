<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class DeferredAlpineComponents extends AbstractFieldArray
{
    protected function _prepareToRender()
    {
        $this->addColumn('selector', ['label' => __('Selector'), 'class' => 'required-entry']);
        $this->addColumn('defer_until', ['label' => __('Defer until'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];

        $row->setData('option_extra_attrs', $options);
    }
}
