<?php

/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Framework\View\Model\Layout;

use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Layout\LayoutCacheKeyInterface;

/**
 * This change adds new event to the layout merging process, so it is easier to add hyva_ prefixed layout update
 * handles. Unfortunately this task was not possible to be handled using either already existent events or plugins.
 */
class Merge extends \Magento\Framework\View\Model\Layout\Merge
{
    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\Url\ScopeResolverInterface $scopeResolver,
        \Magento\Framework\View\File\CollectorInterface $fileSource,
        \Magento\Framework\View\File\CollectorInterface $pageLayoutFileSource,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\Cache\FrontendInterface $cache,
        \Magento\Framework\View\Model\Layout\Update\Validator $validator,
        \Psr\Log\LoggerInterface $logger,
        ReadFactory $readFactory,
        EventManager $eventManager,
        \Magento\Framework\View\Design\ThemeInterface $theme = null,
        $cacheSuffix = '',
        LayoutCacheKeyInterface $layoutCacheKey = null,
        SerializerInterface $serializer = null,
        ?int $cacheLifetime = null
    ) {
        parent::__construct(
            $design,
            $scopeResolver,
            $fileSource,
            $pageLayoutFileSource,
            $appState,
            $cache,
            $validator,
            $logger,
            $readFactory,
            $theme,
            $cacheSuffix,
            $layoutCacheKey,
            $serializer,
            $cacheLifetime
        );
        $this->eventManager = $eventManager;
    }

    /**
     * @inheritDoc
     */
    protected function _loadFileLayoutUpdatesXml()
    {
        $layoutXml = parent::_loadFileLayoutUpdatesXml();
        $this->eventManager->dispatch('load_file_layout_updates_xml_after', ['layoutXml' => $layoutXml]);
        return $layoutXml;
    }
}
