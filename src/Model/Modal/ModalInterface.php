<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

interface ModalInterface
{
    public function isOverlayDisabled(): bool;

    public function getOverlayClasses(): string;

    public function getContainerClasses(): string;

    public function getContentHtml(): string;

    public function isInitiallyHidden(): bool;

    public function getDialogRefName(): string;

    public function getAriaLabelledby(): ?string;

    public function getAriaLabel(): ?string;

    public function getDialogClasses(): string;

    public function render(): string;

    public function __toString();
}
