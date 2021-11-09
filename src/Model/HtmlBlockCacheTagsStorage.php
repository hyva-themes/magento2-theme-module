<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\Framework\App\Cache\Type\Block as BlockCache;
use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\PageCache\Model\Config as PageCacheConfig;

use function array_filter as filter;
use function array_map as map;
use function array_merge as merge;
use function array_unique as unique;
use function array_values as values;

/**
 * Allow html-cached blocks to also be ESI-cached blocks with all their cache tags.
 *
 * This solves the following scenario:
 * Assume a block B calculates it's cache tags dynamically when it is rendered. This happens a lot, e.g. the
 * tags for all loaded products.
 *
 * 1. Block B is rendered and stored in the block_html cache with all cache tags in a normal frontend request.
 * 2. In an Varnish ESI request, the block B is loaded from the cache.
 * 3. Now Block B cache tags are empty because it was cached, the cache tags where not calculated.
 * 4. Varnish now stores the block output for the ESI request without any cache tags.
 * 5. A cache clean request is made for one of the blocks cache tags.
 * 6. The block_html cache record is cleared correctly.
 * 7. The Varnish ESI record is not cleared because it didn't have the tags set on it.
 *
 * This class is used to cache the serialized tags for the appropriate blocks in a separate block_html
 * cache record, after a page is rendered.
 * When such a block is then requested as an ESI, this class is used to fetch the cache tags.
 */
class HtmlBlockCacheTagsStorage
{
    private const KEY_SUFFIX = '-tags';

    /**
     * @var CacheState
     */
    private $cacheState;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PageCacheConfig
     */
    private $pageCacheConfig;

    public function __construct(
        CacheState $cacheState,
        PageCacheConfig $pageCacheConfig,
        Cache $cache,
        SerializerInterface $serializer
    ) {
        $this->cacheState      = $cacheState;
        $this->cache           = $cache;
        $this->serializer      = $serializer;
        $this->pageCacheConfig = $pageCacheConfig;
    }

    public function save(BlockInterface $block): void
    {
        if ($block instanceof AbstractBlock && $this->isDoubleCachedBlock($block)) {
            $this->saveBlockCacheTags($block);
        }
    }

    /**
     * True if the block is cached in both the BLOCK_HTML cache and as a Varnish ESI.
     */
    private function isDoubleCachedBlock(AbstractBlock $block): bool
    {
        return $block->getData('ttl') > 0 && $block->getData('cache_lifetime') !== null;
    }

    private function saveBlockCacheTags(AbstractBlock $block): void
    {
        $tags = unique($this->collectCacheTagsFromBlockAndChildren($block));
        if ($tags && $this->isBlockCacheEnabled() && $this->isVarnishEnabled()) {
            $data = $this->serializer->serialize($tags);
            $this->cache->save($data, $this->getTagsCacheKey($block), merge($tags, [BlockCache::CACHE_TAG]));
        }
    }

    private function collectCacheTagsFromBlockAndChildren(AbstractBlock $block): array
    {
        $tags     = $block->getData('cache_tags');
        $children = values(filter(map([$block->getLayout(), 'getBlock'], $block->getChildNames() ?? [])));

        return merge(is_array($tags) ? $tags : [], ...map([$this, 'collectCacheTagsFromBlockAndChildren'], $children));
    }

    private function getTagsCacheKey(AbstractBlock $block): string
    {
        return $block->getCacheKey() . self::KEY_SUFFIX;
    }

    public function load(AbstractBlock $block): array
    {
        return $this->isBlockCacheEnabled() && $this->isDoubleCachedBlock($block)
            ? $this->getBlockCacheTagsFromCache($block)
            : [];
    }

    private function getBlockCacheTagsFromCache(AbstractBlock $block): array
    {
        $cachedData = $this->cache->load($this->getTagsCacheKey($block));
        return $cachedData !== false
            ? $this->serializer->unserialize($cachedData)
            : [];
    }

    private function isBlockCacheEnabled(): bool
    {
        return $this->cacheState->isEnabled(BlockCache::TYPE_IDENTIFIER);
    }

    private function isVarnishEnabled(): bool
    {
        return $this->pageCacheConfig->getType() == PageCacheConfig::VARNISH;
    }
}
