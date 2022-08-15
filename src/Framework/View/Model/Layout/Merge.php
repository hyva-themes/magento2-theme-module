<?php

/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Framework\View\Model\Layout;

use Magento\Framework\App\State;
use Magento\Framework\Cache\FrontendInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Url\ScopeResolverInterface;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\File\CollectorInterface;
use Magento\Framework\View\Layout\LayoutCacheKeyInterface;
use Magento\Framework\View\Model\Layout\Update\Validator;
use Psr\Log\LoggerInterface;

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
        DesignInterface $design,
        ScopeResolverInterface $scopeResolver,
        CollectorInterface $fileSource,
        CollectorInterface $pageLayoutFileSource,
        State $appState,
        FrontendInterface $cache,
        Validator $validator,
        LoggerInterface $logger,
        ReadFactory $readFactory,
        EventManager $eventManager,
        ThemeInterface $theme = null,
        $cacheSuffix = '',
        LayoutCacheKeyInterface $layoutCacheKey = null,
        SerializerInterface $serializer = null,
        ?int $cacheLifetime = null
    ) {
        // Call the parent constructor using the class name instead of the `parent::` keyword
        // to work around the artificial forward compatibility constraint in
        // \Magento\Framework\Code\Validator\ConstructorIntegrity triggered during setup:di:compile in
        // Magento versions <= 2.4.1.
        // More details can be found in issue https://gitlab.hyva.io/hyva-themes/magento2-theme-module/-/issues/85
        \Magento\Framework\View\Model\Layout\Merge::__construct(
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

    protected function _loadFileLayoutUpdatesXml()
    {
        $layoutXml = parent::_loadFileLayoutUpdatesXml();
        $this->eventManager->dispatch('load_file_layout_updates_xml_after', ['layoutXml' => $layoutXml]);
        return $layoutXml;
    }
}
