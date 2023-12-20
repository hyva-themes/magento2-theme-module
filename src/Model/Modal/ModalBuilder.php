<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
        'overlay-classes'     => ['fixed', 'inset-0', 'bg-black', 'bg-opacity-50', 'z-50'],
        'container-classes'   => ['fixed', 'flex', 'justify-center', 'items-center', 'text-left', 'z-10'],
        'position'            => 'center',
        'dialog-name'         => 'dialog',
        'dialog-classes'      => ['inline-block', 'bg-white', 'shadow-xl', 'rounded-lg', 'p-10', 'max-h-screen', 'overflow-auto'],
        'aria-labelledby'     => null,
        'aria-label'          => null,
        'content-template'    => null,
        'content-block-name'  => null,
        'content'             => null,
    ];

    private $positionClasses = [
        'top'          => ['inset-x-0', 'top-0', 'pt-1'],
        'right'        => ['inset-y-0', 'right-0', 'pr-1'],
        'bottom'       => ['inset-x-0', 'bottom-0', 'pb-1'],
        'left'         => ['inset-y-0', 'left-0', 'pl-1'],
        'center'       => ['inset-0'],
        'top-left'     => ['left-0', 'top-0', 'pt-1', 'pl-1'],
        'top-right'    => ['top-0', 'right-0', 'pt-1', 'pr-1'],
        'bottom-right' => ['bottom-0', 'right-0', 'pb-1', 'pr-1'],
        'bottom-left'  => ['bottom-0', 'left-0', 'pb-1', 'pl-1'],
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

    public function __construct(LayoutInterface $layout, array $data = null, Escaper $escaper = null)
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

    public function overlayEnabled(): ModalBuilderInterface
    {
        return $this->withData('overlay', true);
    }

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

    public function withOverlayClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData('overlay-classes', $classes);
    }

    public function addOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->addClasses('overlay-classes', merge([$class], $moreClasses));
    }

    public function removeOverlayClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->removeClasses('overlay-classes', merge([$class], $moreClasses));
    }

    public function withContainerTemplate(string $template): ModalBuilderInterface
    {
        return $this->withData('container-template', $template);
    }

    public function withContainerClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData('container-classes', $classes);
    }

    public function addContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->addClasses('container-classes', merge([$class], $moreClasses));
    }

    public function removeContainerClass(string $class, string ...$moreClasses): ModalBuilderInterface
    {
        return $this->removeClasses('container-classes', merge([$class], $moreClasses));
    }

    public function positionTop(): ModalBuilderInterface
    {
        return $this->withData('position', 'top');
    }

    public function positionRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'right');
    }

    public function positionBottom(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom');
    }

    public function positionLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'left');
    }

    public function positionCenter(): ModalBuilderInterface
    {
        return $this->withData('position', 'center');
    }

    public function positionTopLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'top-left');
    }

    public function positionTopRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'top-right');
    }

    public function positionBottomRight(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom-right');
    }

    public function positionBottomLeft(): ModalBuilderInterface
    {
        return $this->withData('position', 'bottom-left');
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
        return sprintf("show('%s', %s)", $this->getDialogRefName(), $focusAfterHide ? "'" . $this->escaper->escapeJs($focusAfterHide) . "'" : '$event');
    }

    // --- modal interface methods ---

    public function getOverlayClasses(): string
    {
        return $this->data['overlay'] ? implode(' ', $this->data['overlay-classes']) : '';
    }

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
        return implode(' ', $this->data['dialog-classes']);
    }

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
