<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\ViewModel\PageJsDependencyRegistry;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

use function array_filter as filter;

class RegisterPageJsDependencies implements ObserverInterface
{
    /**
     * @var PageJsDependencyRegistry
     */
    private $jsDependencyRegistry;

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        PageJsDependencyRegistry $jsDependencyRegistry,
        LayoutInterface $layout
    ) {
        $this->jsDependencyRegistry = $jsDependencyRegistry;
        $this->layout = $layout;
    }

    /**
     * Event observer for view_block_abstract_to_html_after.
     */
    public function execute(Event $event)
    {
        $this->applyBlockJsDependencies($event);
        $this->applyBlockOutputPatternJsDependencyRules($event);
    }

    private function applyBlockJsDependencies(Event $event): void
    {
        /** @var AbstractBlock $block */
        if (! ($block = $event->getData('block'))) {
            return; // Container
        }

        $blockNameToPriorityMap = $block->getData('hyva_js_block_dependencies') ?? [];

        if ($blockNameToPriorityMap && is_array($blockNameToPriorityMap)) {
            filter($blockNameToPriorityMap, static function ($value): bool {
                return $value === 0 || $value; // allow 0 as value. Remove false, empty strings and nulls.
            });
            foreach ($blockNameToPriorityMap as $blockName => $priority) {
                $jsBlock = $this->layout->getBlock($blockName);
                if ($jsBlock instanceof AbstractBlock) {
                    $this->jsDependencyRegistry->requireBlock($jsBlock, (int) $priority);
                }
            }
        }
    }

    private function applyBlockOutputPatternJsDependencyRules(Event $event): void
    {
        $blockHtml = $event->getData('transport')->getData('html');
        /** @var Template $jsDependenciesBlock */
        $jsDependenciesBlock = $this->layout->getBlock('page-js-dependencies');

        if ($blockHtml && $jsDependenciesBlock) {
            if ($blockOutputPatternMap = $jsDependenciesBlock->getData('blockOutputPatternMap')) {
                $this->jsDependencyRegistry->applyBlockOutputPatternRules($blockOutputPatternMap, $blockHtml);
            }
        }
    }
}
