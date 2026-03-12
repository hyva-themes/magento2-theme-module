<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Modal;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template as TemplateBlock;
use Magento\Framework\View\LayoutInterface;

use function array_filter as filter;
use function array_merge as merge;
use function array_unique as unique;

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ModalBuilder implements ModalBuilderInterface, ModalInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Note the z-10 class is part of both the overlay-classes and container-classes.
     * With a backdrop the z-index is required on the overlay element, without a backdrop on the container.
     * This is needed because otherwise the store-switcher overlays the dialog.
     *
     * @var mixed[]
     */
    private $defaults = [
        'overlay'             => true, // mask background when dialog is visible
        'is-initially-hidden' => true,
        'container-template'  => 'Hyva_Theme::modal/modal-container.phtml',
        'overlay-classes'     => ['fixed', 'inset-0', 'bg-black/50', 'z-50'],
        'container-classes'   => ['fixed', 'flex', 'justify-center', 'items-center', 'text-left', 'z-40'],
        'position'            => 'center',
        'dialog-name'         => 'dialog',
        'closeby'             => 'any',
        'dialog-classes'      => [],
        'aria-labelledby'     => null,
        'aria-label'          => null,
        'content-template'    => null,
        'content-block-name'  => null,
        'content'             => null,
    ];

    private $positionClasses = [
        'none'         => [],
        'top'          => ['mt-1'],
        'right'        => ['me-1'],
        'bottom'       => ['mb-1'],
        'left'         => ['ms-1'],
        'center'       => [],
        'top-left'     => ['mt-1', 'ms-1'],
        'top-right'    => ['mt-1', 'me-1'],
        'bottom-right' => ['mb-1', 'me-1'],
        'bottom-left'  => ['mb-1', 'ms-1'],
    ];

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var TemplateBlock
     */
    private $memoizedRenderer;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(LayoutInterface $layout, ?array $data = null, ?Escaper $escaper = null)
    {
        $this->layout  = $layout;
        $this->escaper = $escaper ?? ObjectManager::getInstance()->get(Escaper::class);
        $this->data    = merge($this->defaults, $this->data, $data);
    }

    // --- modal builder interface methods ---

    private function withData(string $key, $value): ModalBuilderInterface
    {
        $this->data[$key] = $value;
        return $this;
    }

    private function addClasses(string $key, array $toAdd): ModalBuilderInterface
    {
        $classes = merge($this->data[$key], $toAdd);
        return $this->withData($key, $classes);
    }

    private function removeClasses(string $key, array $toRemove): ModalBuilderInterface
    {
        $classes = filter($this->data[$key], function (string $class) use ($toRemove): bool {
            return ! in_array($class, $toRemove, true);
        });
        return $this->withData($key, $classes);
    }

    /**
     * @deprecated Overlay is now rendered via the native <dialog> backdrop.
     * Use Tailwind's backdrop:* utilities to style it instead.
     */
    public function overlayEnabled(): ModalBuilderInterface
    {
        return $this->withData('overlay', true);
    }

    /**
     * @deprecated Overlay is now rendered via the native <dialog> backdrop.
     * To disable the backdrop, use Tailwind: backdrop:hidden
     */
    public function overlayDisabled(): ModalBuilderInterface
    {
        return $this->withData('overlay', false);
    }

    public function initiallyHidden(): ModalBuilderInterface
    {
        return $this->withData('is-initially-hidden', true);
    }

    public function initiallyVisible(): ModalBuilderInterface
    {
        return $this->withData('is-initially-hidden', false);
    }

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function withOverlayClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData('overlay-classes', $classes);
    }

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function addOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->addClasses('overlay-classes', merge([$class], $moreClasses));
    }

    /**
     * @deprecated Overlay classes are no longer rendered. Use Tailwind's backdrop:* utilities instead.
     */
    public function removeOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->removeClasses('overlay-classes', merge([$class], $moreClasses));
    }

    public function withContainerTemplate(string $template): ModalBuilderInterface
    {
        return $this->withData('container-template', $template);
    }

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function withContainerClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData('container-classes', $classes);
    }

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function addContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->addClasses('container-classes', merge([$class], $moreClasses));
    }

    /**
     * @deprecated Container classes are no longer rendered. The <dialog> element handles its own positioning.
     */
    public function removeContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->removeClasses('container-classes', merge([$class], $moreClasses));
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionNone(): ModalBuilderInterface
    {
        return $this->withData('position', 'none');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTop(): ModalBuilderInterface
    {
        return $this->withData('position', 'top');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'right');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottom(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'left');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionCenter(): ModalBuilderInterface
    {
        return $this->withData('position', 'center');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTopLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'top-left');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionTopRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'top-right');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottomRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom-right');
    }

    /**
     * @deprecated This method still works and applies margin-based positioning to the <dialog> element.
     * Prefer using addDialogClass() to apply positioning classes directly instead.
     */
    public function positionBottomLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom-left');
    }

    public function withCloseby(string $value): ModalBuilderInterface
    {
        return $this->withData('closeby', $value);
    }

    public function withDialogRefName(string $refName): ModalBuilderInterface
    {
        return $this->withData('dialog-name', $refName);
    }

    public function withDialogClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData('dialog-classes', $classes);
    }

    public function addDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->addClasses('dialog-classes', merge([$class], $moreClasses));
    }

    public function removeDialogClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->removeClasses('dialog-classes', merge([$class], $moreClasses));
    }

    /**
     * @deprecated Native <dialog> handles focus trapping automatically. Calling this method has no effect.
     */
    public function excludeSelectorsFromFocusTrapping(string ...$selectors): ModalBuilderInterface
    {
        $current = $this->data['focus-trap-exclude-selectors'] ?? [];

        return $this->withData('focus-trap-exclude-selectors', unique(merge($current, $selectors)));
    }

    public function withAriaLabel(?string $label): ModalBuilderInterface
    {
        return $this->withData('aria-label', $label);
    }

    public function withAriaLabelledby(?string $elementId): ModalBuilderInterface
    {
        return $this->withData('aria-labelledby', $elementId);
    }

    public function withTemplate(?string $template): ModalBuilderInterface
    {
        return $this->withData('content-template', $template);
    }

    public function withBlockName(?string $blockName): ModalBuilderInterface
    {
        return $this->withData('content-block-name', $blockName);
    }

    public function withContent(?string $content): ModalBuilderInterface
    {
        return $this->withData('content', $content);
    }

    public function getShowJs(?string $focusAfterHide = null): string
    {
        $focusAfterHideJs = $focusAfterHide ? "'" . $this->escaper->escapeJs($focusAfterHide) . "'" : 'this.$event || $event';
        return sprintf("show('%s', %s)", $this->getDialogRefName(), $focusAfterHideJs);
    }

    // --- modal interface methods ---

    /**
     * @deprecated Overlay classes are no longer rendered by the default template. Use Tailwind's backdrop:* utilities instead.
     */
    public function getOverlayClasses(): string
    {
        return $this->data['overlay'] ? implode(' ', $this->data['overlay-classes']) : '';
    }

    /**
     * @deprecated Container classes are no longer rendered by the default template.
     */
    public function getContainerClasses(): string
    {
        $classes = merge(
            $this->data['container-classes'],
            $this->positionClasses[$this->data['position']],
        );
        return implode(' ', $classes);
    }

    public function isOverlayDisabled(): bool
    {
        return ! $this->data['overlay'];
    }

    /**
     * @return TemplateBlock|BlockInterface
     */
    public function getContentRenderer(): TemplateBlock
    {
        if (! isset($this->memoizedRenderer)) {
            $this->memoizedRenderer = $this->data['content-block-name']
                ? $this->layout->getBlock($this->data['content-block-name'])
                : $this->layout->createBlock(TemplateBlock::class);
        }
        return $this->memoizedRenderer;
    }

    private function renderContent(): string
    {
        $block = $this->getContentRenderer();
        if ($this->data['content-template']) {
            $block->setTemplate($this->data['content-template']);
        }
        $block->assign('modal', $this);

        return $block->toHtml();
    }

    public function getContentHtml(): string
    {
        return $this->data['content'] ?? $this->renderContent();
    }

    public function isInitiallyHidden(): bool
    {
        return $this->data['is-initially-hidden'];
    }

    public function getCloseby(): string
    {
        return $this->data['closeby'];
    }

    public function getDialogRefName(): string
    {
        return $this->data['dialog-name'];
    }

    public function getAriaLabelledby(): ?string
    {
        return $this->data['aria-labelledby'];
    }

    public function getAriaLabel(): ?string
    {
        return $this->data['aria-label'];
    }

    public function getDialogClasses(): string
    {
        $classes = merge(
            $this->data['dialog-classes'],
            $this->positionClasses[$this->data['position']],
            $this->data['overlay'] ? [] : ['backdrop:hidden'],
        );
        return implode(' ', $classes);
    }

    /**
     * @deprecated Native <dialog> handles focus trapping automatically.
     */
    public function getFocusTrapExcludeSelectors(): array
    {
        return $this->data['focus-trap-exclude-selectors'] ?? [];
    }

    public function render(): string
    {
        $block = $this->layout->createBlock(TemplateBlock::class);
        $block->setTemplate($this->data['container-template']);
        $block->assign('modal', $this);

        return $block->toHtml();
    }

    public function __toString()
    {
        return $this->render();
    }
}
