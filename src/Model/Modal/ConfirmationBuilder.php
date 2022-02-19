<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

class ConfirmationBuilder extends ModalBuilder implements ConfirmationBuilderInterface
{
    public function withTitle($title): ConfirmationBuilderInterface
    {
        $this->withAriaLabel($title ? (string) $title : null);
        $this->getContentRenderer()->setData('title', $title);

        return $this;
    }

    public function withDetails($details): ConfirmationBuilderInterface
    {
        $this->getContentRenderer()->setData('details', $details);

        return $this;
    }

    public function withOKLabel($okLabel): ConfirmationBuilderInterface
    {
        $this->getContentRenderer()->setData('ok', $okLabel);

        return $this;
    }

    public function withCancelLabel($cancelLabel): ConfirmationBuilderInterface
    {
        $this->getContentRenderer()->setData('cancel', $cancelLabel);

        return $this;
    }
}
