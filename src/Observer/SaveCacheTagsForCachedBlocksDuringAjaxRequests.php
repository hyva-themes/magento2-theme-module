<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Model\HtmlBlockCacheTagsStorage;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Element\AbstractBlock;

class SaveCacheTagsForCachedBlocksDuringAjaxRequests implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var HtmlBlockCacheTagsStorage
     */
    private $blockCacheTagsStorage;

    public function __construct(HtmlBlockCacheTagsStorage $blockCacheTagsStorage, RequestInterface $request)
    {
        $this->blockCacheTagsStorage = $blockCacheTagsStorage;
        $this->request = $request;
    }

    public function execute(Event $event)
    {
        if ($this->isEsiRequest()) {
            /** @var AbstractBlock $block */
            $block = $event->getData('block');
            $this->blockCacheTagsStorage->save($block);
        }
    }

    private function isEsiRequest(): bool
    {
        return $this->request->getModuleName() === 'page_cache' && $this->request->getControllerName() === 'block' && $this->request->getActionName() === 'esi';
    }
}
