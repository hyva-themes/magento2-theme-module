<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

use function array_map as map;
use function array_merge as merge;
use function array_reduce as reduce;
use function array_unique as unique;

class BlockCache implements ArgumentInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    public function __construct(JsonSerializer $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * @param scalar[] $cacheInfo
     */
    public function hashCacheKeyInfo(array $cacheInfo): string
    {
        return sha1($this->jsonSerializer->serialize($cacheInfo));
    }

    /**
     * @param AbstractBlock $block
     * @return string[]
     */
    public function getCacheTagsFromBlock(AbstractBlock $block): array
    {
        $tags = $block->getData('cache_tags') ?? [];
        return $block instanceof IdentityInterface
            ? unique(merge($tags, $block->getIdentities() ?: []))
            : $tags;
    }

    /**
     * @param AbstractBlock $block
     * @return string[]
     */
    public function collectBlockChildrenCacheTags(AbstractBlock $block): array
    {
        $childBlocks = map([$block->getLayout(), 'getBlock'], $block->getChildNames());
        return reduce($childBlocks, function (array $tags, AbstractBlock $block) {
            return merge($tags, $this->getCacheTagsFromBlock($block));
        }, []);
    }
}
