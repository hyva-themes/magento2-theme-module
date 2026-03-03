<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Adminhtml\Form\Field;

use Magento\Backend\Block\AbstractBlock;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Note extends AbstractBlock implements RendererInterface
{
    /**
     * Render a read-only note row styled like a native Magento field comment.
     */
    public function render(AbstractElement $element): string
    {
        return sprintf(
            '<tr id="row_%s"><td class="label"><label>%s</label></td><td class="value"><p class="note"><span>%s</span></p></td><td colspan="3"></td></tr>',
            $element->getHtmlId(),
            $element->getLabel(),
            $element->getComment()
        );
    }
}
