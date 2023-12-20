<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

interface ModalBuilderInterface
{
    public function overlayEnabled(): ModalBuilderInterface;

    public function overlayDisabled(): ModalBuilderInterface;

    public function initiallyHidden(): ModalBuilderInterface;

    public function initiallyVisible(): ModalBuilderInterface;

    public function withOverlayClasses(string ...$classes): ModalBuilderInterface;

    public function addOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function removeOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function withContainerTemplate(string $template): ModalBuilderInterface;

    public function withContainerClasses(string ...$classes): ModalBuilderInterface;

    public function addContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function removeContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function positionTop(): ModalBuilderInterface;

    public function positionRight(): ModalBuilderInterface;

    public function positionBottom(): ModalBuilderInterface;

    public function positionLeft(): ModalBuilderInterface;

    public function positionCenter(): ModalBuilderInterface;

    public function positionTopLeft(): ModalBuilderInterface;

    public function positionTopRight(): ModalBuilderInterface;

    public function positionBottomRight(): ModalBuilderInterface;

    public function positionBottomLeft(): ModalBuilderInterface;

    public function withDialogRefName(string $refName): ModalBuilderInterface;

    public function getDialogRefName(): string;

    public function withDialogClasses(string ...$classes): ModalBuilderInterface;

    public function addDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function removeDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function excludeSelectorsFromFocusTrapping(string ...$selectors): ModalBuilderInterface;

    public function withAriaLabel(string $label): ModalBuilderInterface;

    public function withAriaLabelledby(string $elementId): ModalBuilderInterface;

    public function withTemplate(string $template): ModalBuilderInterface;

    public function withBlockName(string $blockName): ModalBuilderInterface;

    public function withContent(string $content): ModalBuilderInterface;

    public function getShowJs(?string $focusAfterHide = null): string;
}
