<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageCache;

use Hyva\Theme\Model\HtmlBlockCacheTagsStorage;
use Hyva\Theme\Model\ViewModelCacheTags;
use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\Framework\App\Cache\Type\Block as BlockHtmlCache;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Controller\Block\Esi as EsiController;

use function array_merge as merge;
use function array_unique as unique;
use function array_values as values;

/**
 * Add view model cache tags to responses for varnish ESI requests.
 */
class AddViewModelCacheTagesToEsiResponse
{
    /**
     * @var CacheState
     */
    private $cacheState;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var HtmlBlockCacheTagsStorage
     */
    private $blockCacheTagsStorage;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @var ViewModelCacheTags
     */
    private $viewModelCacheTags;

    public function __construct(
        CacheState $cacheState,
        LayoutInterface $layout,
        JsonSerializer $jsonSerializer,
        RequestInterface $request,
        HtmlBlockCacheTagsStorage $blockCacheTagsStorage,
        ViewModelCacheTags $viewModelCacheTags
    ) {
        $this->cacheState            = $cacheState;
        $this->layout                = $layout;
        $this->jsonSerializer        = $jsonSerializer;
        $this->request               = $request;
        $this->blockCacheTagsStorage = $blockCacheTagsStorage;
        $this->viewModelCacheTags    = $viewModelCacheTags;
    }

    public function afterExecute(EsiController $subject, $result)
    {
        $response = $subject->getResponse();
        if ($response instanceof HttpResponseInterface && $this->isHtmlBlockCacheEnabled()) {
            $existingTags       = $this->extractExistingCacheTags($response);
            $blockHtmlTags      = $this->extractCacheTagsForBlockFromHtmlCacheRecord();
            $viewModelCacheTags = $this->viewModelCacheTags->get();
            $allTags            = merge($existingTags, $blockHtmlTags, $viewModelCacheTags);
            $response->setHeader('X-Magento-Tags', implode(',', unique($allTags)), true);
        }
        return $result;
    }

    private function extractExistingCacheTags(HttpResponseInterface $response): array
    {
        return ($cacheTagsHeader = $response->getHeader('X-Magento-Tags'))
            ? explode(',', $cacheTagsHeader->getFieldValue())
            : [];
    }

    private function extractCacheTagsForBlockFromHtmlCacheRecord(): array
    {
        $block = $this->getRequestedEsiBlock();
        return $block
            ? $this->blockCacheTagsStorage->load($block)
            : [];
    }

    public function getRequestedEsiBlock(): ?AbstractBlock
    {
        $blockName = $this->getRequestedEsiBlockName();

        return $blockName
            ? $this->getBlockFromLayout($blockName)
            : null;
    }

    private function getBlockFromLayout(string $blockName): ?AbstractBlock
    {
        /** @var AbstractBlock $block */
        $block = $this->layout->getBlock($blockName);

        return $block ?: null;
    }

    private function isHtmlBlockCacheEnabled(): bool
    {
        return $this->cacheState->isEnabled(BlockHtmlCache::TYPE_IDENTIFIER);
    }

    /**
     * @see \Magento\PageCache\Controller\Block::_getBlocks
     */
    private function getRequestedEsiBlockName(): ?string
    {
        $blockNames = $this->jsonSerializer->unserialize($this->request->getParam('blocks', ''));

        // ESI requests are always only for a single block;
        $blockName = $blockNames ? values($blockNames)[0] : null;
        return $blockName;
    }
}
