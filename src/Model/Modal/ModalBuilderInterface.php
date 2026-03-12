<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

interface ModalBuilderInterface
{
    /**
     * @deprecated Overlay is now rendered via the native <dialog> backdrop.
     * Use Tailwind's backdrop:* utilities to style it instead.
     */
    public function overlayEnabled(): ModalBuilderInterface;

    /**
     * @deprecated Overlay is now rendered via the native <dialog> backdrop.
     * To disable the backdrop, use Tailwind: backdrop:hidden
     */
    public function overlayDisabled(): ModalBuilderInterface;

    public function initiallyHidden(): ModalBuilderInterface;

    public function initiallyVisible(): ModalBuilderInterface;

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function withOverlayClasses(string ...$classes): ModalBuilderInterface;

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function addOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function removeOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function withContainerTemplate(string $template): ModalBuilderInterface;

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function withContainerClasses(string ...$classes): ModalBuilderInterface;

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function addContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function removeContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionNone(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTop(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionRight(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottom(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionLeft(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionCenter(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTopLeft(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTopRight(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottomRight(): ModalBuilderInterface;

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottomLeft(): ModalBuilderInterface;

    /**
     * Set the value of the native <dialog> closeby attribute.
     * Accepted values: "any" (close on outside click or ESC), "closerequest" (ESC only), "none" (never auto-close).
     */
    public function withCloseby(string $value): ModalBuilderInterface;

    public function getCloseby(): string;

    public function withDialogRefName(string $refName): ModalBuilderInterface;

    public function getDialogRefName(): string;

    public function withDialogClasses(string ...$classes): ModalBuilderInterface;

    public function addDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    public function removeDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface;

    /**
     * @deprecated Native <dialog> handles focus trapping automatically. Calling this method has no effect.
     */
    public function excludeSelectorsFromFocusTrapping(string ...$selectors): ModalBuilderInterface;

    public function withAriaLabel(string $label): ModalBuilderInterface;

    public function withAriaLabelledby(string $elementId): ModalBuilderInterface;

    public function withTemplate(string $template): ModalBuilderInterface;

    public function withBlockName(string $blockName): ModalBuilderInterface;

    public function withContent(string $content): ModalBuilderInterface;

    public function getShowJs(?string $focusAfterHide = null): string;
}
