<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;

/**
 * A registry that can return instances of any view model. They no longer need to be passed to each block via layout XML
 *
 * Available in templates as `$viewModels`. Uses the object manager internally, no need to duplicate its instance cache.
 */
class ViewModelRegistry
{
    private const MAX_TRACE_DEPTH = 14;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ViewModelCacheTags
     */
    private $viewModelCacheTags;

    /**
     * @var PageCacheConfig
     */
    private $pageCacheConfig;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ViewModelCacheTags $viewModelCacheTags,
        PageCacheConfig $pageCacheConfig
    ) {
        $this->objectManager = $objectManager;
        $this->viewModelCacheTags = $viewModelCacheTags;
        $this->pageCacheConfig = $pageCacheConfig;
    }

    /**
     * Returns view model instance for given FQN
     *
     * @template T
     * @param string $viewModelClass Fully qualified class name (FQN)
     * @param AbstractBlock|null $block Only required if view model is used within a template cached in ESI (ttl="n" in layout XML)
     * @return T
     * @throw InvalidViewModelClass if class not found or not a view model
     */
    public function require(string $viewModelClass, AbstractBlock $block = null): ArgumentInterface
    {
        try {
            $object = $this->objectManager->get($viewModelClass);
        } catch (\Exception $e) {
            throw InvalidViewModelClass::notFound($viewModelClass, $e);
        }
        if (!$object instanceof ArgumentInterface) {
            throw InvalidViewModelClass::notAViewModel($viewModelClass);
        }

        // We do not want to collect the cache tags for blocks that will be served via ESI while rendering the main page FPC record.
        // If we do, the main page will be purged for those tags, even though we only want to purge the ESI records.
        // On ESI requests isVarnishEnabled() is false, so we don't need to check for a ttl value.
        // If isVarnishEnabled() is true, this is a main page request (not ESI), so we check if the block is rendered within an ESI section.
        if (! $block || !($this->isVarnishEnabled() && $this->isCalledWithinEsiBlock($block))) {
            $this->viewModelCacheTags->collectFrom($object);
        }

        return $object;
    }

    private function isCalledWithinEsiBlock(AbstractBlock $block): bool
    {
        while ($block instanceof AbstractBlock && ! $block->getTtl()) {
            $block = $block->getParentBlock();
        }
        return $block && $block->getTtl() > 0;
    }

    private function isVarnishEnabled(): bool
    {
        return $this->pageCacheConfig->getType() === PageCacheConfig::VARNISH;
    }
}
