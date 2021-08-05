<?php declare(strict_types=1);
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

namespace Hyva\Theme\Model\Modal;

use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template as TemplateBlock;
use Magento\Framework\View\LayoutInterface;

use function array_merge as merge;
use function array_search as search;
use function array_splice as splice;

class ModalBuilder implements ModalBuilderInterface, ModalInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var mixed[]
     */
    private $defaults = [
        'overlay'             => true, // mask background when dialog is visible
        'is-initially-hidden' => true,
        'container-template'  => 'Hyva_Theme::modal/modal-container.phtml',
        'overlay-classes'     => ['fixed', 'inset-0', 'bg-black', 'bg-opacity-50'],
        'container-classes'   => ['fixed', 'flex', 'justify-center', 'items-center', 'text-left'],
        'position'            => 'center',
        'dialog-name'         => 'dialog',
        'dialog-classes'      => ['inline-block', 'bg-white', 'shadow-xl', 'rounded-lg', 'p-10'],
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

    public function __construct(LayoutInterface $layout, array $data = null)
    {
        $this->layout = $layout;
        $this->data   = merge($this->defaults, $this->data, $data);
    }

    // --- modal builder interface methods ---

    private function withData(array $data, string $key, $value): ModalBuilderInterface
    {
        $data[$key] = $value;
        return new self($this->layout, $data);
    }

    private function addClass(array $data, string $key, string $class): ModalBuilderInterface
    {
        $classes   = $this->data[$key];
        $classes[] = $class;
        return $this->withData($data, $key, $classes);
    }

    private function removeClass(array $data, string $key, string $class): ModalBuilderInterface
    {
        $classes = $this->data[$key];
        $pos     = search($class, $classes, true);
        if ($pos !== false) {
            splice($classes, $pos, 1);
        }
        return $this->withData($data, $key, $classes);
    }

    public function overlayEnabled(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'overlay', true);
    }

    public function overlayDisabled(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'overlay', false);
    }

    public function initiallyHidden(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'is-initially-hidden', true);
    }

    public function initiallyVisible(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'is-initially-hidden', false);
    }

    public function withOverlayClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData($this->data, 'overlay-classes', $classes);
    }

    public function addOverlayClass(string $class): ModalBuilderInterface
    {
        return $this->addClass($this->data, 'overlay-classes', $class);
    }

    public function removeOverlayClass(string $class): ModalBuilderInterface
    {
        return $this->removeClass($this->data, 'overlay-classes', $class);
    }

    public function withContainerTemplate(string $template): ModalBuilderInterface
    {
        return $this->withData($this->data, 'container-template', $template);
    }

    public function withContainerClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData($this->data, 'container-classes', $classes);
    }

    public function addContainerClass(string $class): ModalBuilderInterface
    {
        return $this->addClass($this->data, 'container-classes', $class);
    }

    public function removeContainerClass(string $class): ModalBuilderInterface
    {
        return $this->removeClass($this->data, 'container-classes', $class);
    }

    public function positionTop(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'top');
    }

    public function positionRight(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'right');
    }

    public function positionBottom(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'bottom');
    }

    public function positionLeft(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'left');
    }

    public function positionCenter(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'center');
    }

    public function positionTopLeft(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'top-left');
    }

    public function positionTopRight(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'top-right');
    }

    public function positionBottomRight(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'bottom-right');
    }

    public function positionBottomLeft(): ModalBuilderInterface
    {
        return $this->withData($this->data, 'position', 'bottom-left');
    }

    public function withDialogRefName(string $refName): ModalBuilderInterface
    {
        return $this->withData($this->data, 'dialog-name', $refName);
    }

    public function withDialogClasses(string ...$classes): ModalBuilderInterface
    {
        return $this->withData($this->data, 'dialog-classes', $classes);
    }

    public function addDialogClass(string $class): ModalBuilderInterface
    {
        return $this->addClass($this->data, 'dialog-classes', $class);
    }

    public function removeDialogClass(string $class): ModalBuilderInterface
    {
        return $this->removeClass($this->data, 'dialog-classes', $class);
    }

    public function withAriaLabel(?string $label): ModalBuilderInterface
    {
        return $this->withData($this->data, 'aria-label', $label);
    }

    public function withAriaLabelledby(?string $elementId): ModalBuilderInterface
    {
        return $this->withData($this->data, 'aria-labelledby', $elementId);
    }

    public function withTemplate(?string $template): ModalBuilderInterface
    {
        return $this->withData($this->data, 'content-template', $template);
    }

    public function withBlockName(?string $blockName): ModalBuilderInterface
    {
        return $this->withData($this->data, 'content-block-name', $blockName);
    }

    public function withContent(?string $content): ModalBuilderInterface
    {
        return $this->withData($this->data, 'content', $content);
    }

    public function getShowJs(): string
    {
        return sprintf("show('%s', \$event)", $this->getDialogRefName());
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
    private function getContentRenderer(): TemplateBlock
    {
        return $this->data['content-block-name']
            ? $this->layout->getBlock($this->data['content-block-name'])
            : $this->layout->createBlock(TemplateBlock::class);
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
