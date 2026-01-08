<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

use Magento\Framework\Phrase;

interface ConfirmationBuilderInterface extends ModalBuilderInterface
{
    /**
     * @param string|Phrase|null $title
     * @return ConfirmationBuilderInterface
     */
    public function withTitle($title): ConfirmationBuilderInterface;

    /**
     * @param string|Phrase|null $details
     * @return ConfirmationBuilderInterface
     */
    public function withDetails($details): ConfirmationBuilderInterface;

    /**
     * @param string|Phrase|null $okLabel
     * @return ConfirmationBuilderInterface
     */
    public function withOKLabel($okLabel): ConfirmationBuilderInterface;

    /**
     * @param string|Phrase|null $cancelLabel
     * @return ConfirmationBuilderInterface
     */
    public function withCancelLabel($cancelLabel): ConfirmationBuilderInterface;
}
