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
     * @deprecated The "none" position was intended for manual dialog placement with withContainerClasses().
     * The container element no longer exists. Use addDialogClass() to apply positioning classes to the
     * <dialog> element directly instead.
     */
    public function positionNone(): ModalBuilderInterface;

    public function positionTop(): ModalBuilderInterface;

    public function positionRight(): ModalBuilderInterface;

    public function positionBottom(): ModalBuilderInterface;

    public function positionLeft(): ModalBuilderInterface;

    public function positionCenter(): ModalBuilderInterface;

    public function positionTopLeft(): ModalBuilderInterface;

    public function positionTopRight(): ModalBuilderInterface;

    public function positionBottomRight(): ModalBuilderInterface;

    public function positionBottomLeft(): ModalBuilderInterface;

    /**
     * Set the value of the native <dialog> closeby attribute.
     * Accepted values: "any" (close on outside click or ESC), "closerequest" (ESC only), "none" (never auto-close).
     *
     * @since 1.5.0
     */
    public function withCloseby(string $value): ModalBuilderInterface;

    /**
     * @since 1.5.0
     */
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
