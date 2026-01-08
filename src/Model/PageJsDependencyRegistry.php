<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

use function array_keys as keys;
use function array_map as map;

class PageJsDependencyRegistry
{
    public const DEFAULT_PRIORITY = 10;

    /**
     * The first entry starts has the index 1.
     *
     * @var array[]
     */
    private $jsDependencies = [];

    /**
     * Map template identifier string to jsDependencies index
     *
     * @var int[]
     */
    private $templateIndex = [];

    /**
     * Map block name string to jsDependencies index
     *
     * @var int[]
     */
    private $blockIndex = [];

    /**
     * @var Template|null
     */
    private $memoizedDefaultTemplateRenderer;

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * Render the given template on in the footer of the page.
     *
     * Blocks and templates are rendered in ascending order of priority.
     */
    public function requireTemplate(string $template, $priority = null): void
    {
        $idx = $this->templateIndex[$template] ?? $this->createIndex();
        $this->templateIndex[$template] = $idx;
        $this->jsDependencies[$idx] = [
            'template' => $template,
            'priority' => $priority ?? $this->jsDependencies[$idx]['priority'] ?? self::DEFAULT_PRIORITY
        ];
    }

    /**
     * Render the given block on in the footer of the page.
     *
     * Blocks and templates are rendered in ascending order of priority.
     */
    public function requireBlockName(string $blockName, $priority = null): void
    {
        if (! ($block = $this->layout->getBlock($blockName))) {
            return;
        }
        $idx = $this->blockIndex[$blockName] ?? $this->createIndex();
        $this->blockIndex[$blockName] = $idx;
        $this->jsDependencies[$idx] = [
            'block' => $block,
            'priority' => $priority ?? $this->jsDependencies[$idx]['priority'] ?? self::DEFAULT_PRIORITY
        ];
    }

    public function setRendererForTemplate(string $template, AbstractBlock $block): void
    {
        if ($idx = $this->templateIndex[$template]) {
            $this->jsDependencies[$idx]['block'] = $block;
            if ($name = $block->getNameInLayout()) {
                $this->blockIndex[$name] = $idx;
            }
        }
    }

    public function removeTemplate(string $template): void
    {
        if ($idx = $this->templateIndex[$template]) {
            if ($block = $this->jsDependencies[$idx]['block'] ?? false) {
                unset($this->blockIndex[$block->getNameInLayout()]);
            }
            unset($this->templateIndex[$template]);
            unset($this->jsDependencies[$idx]);
        }
    }

    public function removeBlock(string $bockName): void
    {
        if ($bockName && ($idx = $this->blockIndex[$bockName])) {
            if ($template = $this->jsDependencies[$idx]['template'] ?? false) {
                unset($this->templateIndex[$template]);
            }
            unset($this->blockIndex[$bockName]);
            unset($this->jsDependencies[$idx]);
        }
    }

    /**
     * Return all js dependencies without sorting. Intended to be used as an after plugin target.
     *
     * Each item in the returned array may have the following keys
     *
     * [
     *   'template' => string 'Some_Module::template.phtml',
     *   'block' => AbstractBlock instance,
     *   'priority' => int sort order (higher = rendered later)
     * ]
     *
     * @return array[]
     */
    public function getUnsortedJsDependencies(): array
    {
        return $this->jsDependencies;
    }

    private function getSortedJsDependencies(): array
    {
        return $this->sortJsDependencies($this->getUnsortedJsDependencies());
    }

    private function getLastIndex(): ?int
    {
        if ($this->jsDependencies) {
            $keys = keys($this->jsDependencies);
            return end($keys);
        }
        return null;
    }

    private function createIndex(): int
    {
        $idx = ($this->getLastIndex() ?? 0) + 1;
        $this->jsDependencies[$idx] = [];
        return $idx;
    }

    private function sortJsDependencies(array $jsDependencies): array
    {
        uasort($jsDependencies, function (array $a, array $b) {
            return (int) $a['priority'] <=> (int) $b['priority'];
        });

        return $jsDependencies;
    }

    public function getJsDependenciesHtml(): string
    {
        return implode('', map([$this, 'renderJsDependencyHtml'], $this->getSortedJsDependencies()));
    }

    private function getDefaultTemplateRenderer(): Template
    {
        if (! isset($this->memoizedDefaultTemplateRenderer)) {
            $this->memoizedDefaultTemplateRenderer = $this->layout->createBlock(Template::class);
        }

        return $this->memoizedDefaultTemplateRenderer;
    }

    private function renderJsDependencyHtml(array $jsDependency): string
    {
        $block = $jsDependency['block'] ?? $this->getDefaultTemplateRenderer();
        $origTemplate = $block->getTemplate();
        if (isset($jsDependency['template'])) {
            $block->setTemplate($jsDependency['template']);
        }
        $html = $block->toHtml();
        $block->setTemplate($origTemplate);

        return $html;
    }

    // ----------- process dependencies declared via layout XML arguments for backward compatibility -----------

    /**
     * Called by \Hyva\Theme\Observer\RegisterPageJsDependencies::applyBlockOutputPatternJsDependencyRules.
     *
     * Provides backward compatible conditional loading of PageBuilder JS on the current page.
     * Not intended to be called by anything else. Not covered by backward compatibility guarantee.
     *
     * @internal
     */
    public function applyBlockOutputPatternRules(array $blockOutputPatternMap, string $blockHtml): void
    {
        foreach (keys($blockOutputPatternMap) as $jsDependencyName) {
            if ($this->requireTemplateJsDependency($blockOutputPatternMap[$jsDependencyName], $blockHtml)) {
                // Once a block is registered, there is no need to check for further matches for this pattern
                unset($blockOutputPatternMap[$jsDependencyName]);
            }
        }
    }

    private function requireTemplateJsDependency(array $jsDependencyItem, string $blockHtml): bool
    {
        if ($this->isValidJsTemplateItem($jsDependencyItem) && preg_match($jsDependencyItem['regex'], $blockHtml)) {
            $this->requireTemplate($jsDependencyItem['template'], $jsDependencyItem['priority'] ?? null);
            return true;
        }
        return false;
    }

    private function isValidJsTemplateItem($jsDependencyItem): bool
    {
        return is_array($jsDependencyItem) && ($jsDependencyItem['regex'] ?? false) && ($jsDependencyItem['template'] ?? false);
    }
}
