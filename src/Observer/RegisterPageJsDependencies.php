<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Model\PageJsDependencyRegistry;
use Hyva\Theme\Service\CurrentTheme;
use Hyva\Theme\ViewModel\BlockJsDependencies;
use Magento\Framework\App\Cache\Type\Block as BlockCache;
use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

use function array_filter as filter;
use function array_map as map;
use function array_merge as merge;
use function array_values as values;

class RegisterPageJsDependencies implements ObserverInterface
{
    private const KEY_SUFFIX = '-dependencies';

    /**
     * @var PageJsDependencyRegistry
     */
    private $jsDependencyRegistry;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var CacheState
     */
    private $cacheState;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    public function __construct(
        PageJsDependencyRegistry $jsDependencyRegistry,
        LayoutInterface $layout,
        CacheState $cacheState,
        CacheInterface $cache,
        ?CurrentTheme $currentTheme = null
    ) {
        $this->jsDependencyRegistry = $jsDependencyRegistry;
        $this->layout = $layout;
        $this->cacheState = $cacheState;
        $this->cache = $cache;
        $this->currentTheme = $currentTheme ?? ObjectManager::getInstance()->get(CurrentTheme::class);
    }

    /**
     * Event observer for view_block_abstract_to_html_after.
     */
    public function execute(Event $event)
    {
        if ($this->currentTheme->isHyva()) {
            $this->applyBlockJsDependencies($event);
            $this->applyBlockOutputPatternJsDependencyRules($event);
        }
    }

    /**
     * Read dependencies from the blocks and register them in the page js dependency registry, also adding them to the block_html cache if the block is cached.
     */
    private function applyBlockJsDependencies(Event $event): void
    {
        /** @var AbstractBlock $block */
        if (! ($block = $event->getData('block'))) {
            return; // Container
        }

        $cachedDeps = false;
        if ($this->isBlockCacheEnabled() && $this->isBlockCached($block) && ($cachedDeps = $this->cache->load($this->getDependenciesCacheKey($block)))) {
            [$blockNameToPriorityMap, $templateNameToPriorityMap] = json_decode($cachedDeps, true);
        } else {
            $blockNameToPriorityMap = $this->collectBlockNameDependencies($block);
            $templateNameToPriorityMap = $this->collectTemplateDependencies($block);
        }

        $this->addDependencies($blockNameToPriorityMap, [$this->jsDependencyRegistry, 'requireBlockName']);
        $this->addDependencies($templateNameToPriorityMap, [$this->jsDependencyRegistry, 'requireTemplate']);

        if ($this->isBlockCacheEnabled() && $this->isBlockCached($block) && ! $cachedDeps) {
            $key = $this->getDependenciesCacheKey($block);
            $tags = $this->getDependenciesCacheTags($block);
            $data = json_encode([$blockNameToPriorityMap, $templateNameToPriorityMap]);
            $this->cache->save($data, $key, $tags, $block->getData('cache_lifetime'));
        }
    }

    private function addDependencies(?array $dependencyToPriorityMap, callable $addDependencyFn): void
    {
        if ($dependencyToPriorityMap) {
            $dependencyToPriorityMap = filter($dependencyToPriorityMap, static function ($priority): bool {
                return $priority === 0 || $priority; // allow 0 as value. Remove false, empty strings and nulls.
            });
            foreach ($dependencyToPriorityMap as $dependency => $priority) {
                $addDependencyFn($dependency, $priority);
            }
        }
    }

    private function applyBlockOutputPatternJsDependencyRules(Event $event): void
    {
        $blockHtml = (string) $event->getData('transport')->getData('html') ?? '';
        /** @var Template $jsDependenciesBlock */
        $jsDependenciesBlock = $this->layout->getBlock('page-js-dependencies');

        if ($blockHtml && $jsDependenciesBlock) {
            if ($blockOutputPatternMap = $jsDependenciesBlock->getData('blockOutputPatternMap')) {
                $this->jsDependencyRegistry->applyBlockOutputPatternRules($blockOutputPatternMap, $blockHtml);
            }
        }
    }

    private function isBlockCacheEnabled(): bool
    {
        return $this->cacheState->isEnabled(BlockCache::TYPE_IDENTIFIER);
    }

    private function getDependenciesCacheKey(AbstractBlock $block): string
    {
        return $block->getCacheKey() . self::KEY_SUFFIX;
    }

    private function getDependenciesCacheTags(AbstractBlock $block)
    {
        return merge($this->collectCacheTagsFromBlockHierarchy($block) ?? [], [BlockCache::CACHE_TAG]);
    }

    private function isBlockCached(AbstractBlock $block): bool
    {
        $cacheLifetime = $block->getData('cache_lifetime');
        return $cacheLifetime !== null && $cacheLifetime !== false;
    }

    private function collectCacheTagsFromBlockHierarchy(AbstractBlock $block): array
    {
        return $this->collectArrayDataFromBlockHierarchy($block, 'cache_tags');
    }

    private function collectBlockNameDependencies(AbstractBlock $block): ?array
    {
        return $this->isBlockCached($block)
            ? $this->collectArrayDataFromBlockHierarchy($block, BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY)
            : $block->getData(BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY);
    }

    private function collectTemplateDependencies(AbstractBlock $block): ?array
    {
        return $this->isBlockCached($block)
            ? $this->collectArrayDataFromBlockHierarchy($block, BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY)
            : $block->getData(BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY);
    }

    private function collectArrayDataFromBlockHierarchy(AbstractBlock $block, string $dataKey): array
    {

        $collector = function (AbstractBlock $block) use ($dataKey, &$collector): array {
            $data     = $block->getData($dataKey);
            $children = values(filter(map([$block->getLayout(), 'getBlock'], $block->getChildNames() ?? [])));

            return merge(is_array($data) ? $data : [], ...map($collector, $children));
        };

        return $collector($block);
    }
}
