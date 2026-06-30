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

    /**
     * @deprecated Overlay classes are no longer rendered by the default template. Use Tailwind's backdrop:* utilities instead.
     */
    public function getOverlayClasses(): string;

    /**
     * @deprecated Container classes are no longer rendered by the default template.
     */
    public function getContainerClasses(): string;

    public function getContentHtml(): string;

    public function isInitiallyHidden(): bool;

    /**
     * @since 1.5.0
     */
    public function getCloseby(): string;

    public function getDialogRefName(): string;

    public function getAriaLabelledby(): ?string;

    public function getAriaLabel(): ?string;

    public function getDialogClasses(): string;

    /**
     * @deprecated Native <dialog> handles focus trapping automatically.
     * @return string[]
     */
    public function getFocusTrapExcludeSelectors(): array;

    public function render(): string;

    public function __toString();
}
