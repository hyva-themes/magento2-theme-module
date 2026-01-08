<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

use Magento\Framework\View\Element\Template as TemplateBlock;

interface ModalInterface
{
    public function isOverlayDisabled(): bool;

    public function getContentRenderer(): TemplateBlock;

    public function getOverlayClasses(): string;

    public function getContainerClasses(): string;

    public function getContentHtml(): string;

    public function isInitiallyHidden(): bool;

    public function getDialogRefName(): string;

    public function getAriaLabelledby(): ?string;

    public function getAriaLabel(): ?string;

    public function getDialogClasses(): string;

    /**
     * @return string[]
     */
    public function getFocusTrapExcludeSelectors(): array;

    public function render(): string;

    public function __toString();
}
