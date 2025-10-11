<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Laminas\Http\Header\CacheControl;
use Laminas\Http\Header\HeaderInterface;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RemoveNoStoreHeaderPlugin
{
    private const CONFIG_PATH_BFCACHE = 'system/full_page_cache/bfcache';

    /**
     * @var bool
     */
    private $isRequestCacheable = false;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::CONFIG_PATH_BFCACHE, ScopeInterface::SCOPE_STORE);
    }

    private function isRequestCacheable(HeaderInterface $header): bool
    {
        $value = $header->getFieldValue();
        return (bool) preg_match('/public.*s-maxage=(\d+)/', $value);
    }

    public function beforeSetNoCacheHeaders(HttpResponse $subject): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $cacheHeader = $subject->getHeader('Cache-Control');
        if (!$cacheHeader) {
            return;
        }

        $this->isRequestCacheable = $this->isRequestCacheable($cacheHeader);
    }

    public function afterSetNoCacheHeaders(HttpResponse $subject, $result)
    {
        if (!$this->isEnabled()) {
            return $result;
        }

        /**
         * @var CacheControl
         */
        $cacheHeader = $subject->getHeader('Cache-Control');
        if (!$cacheHeader) {
            return $result;
        }

        if ($this->isRequestCacheable) {
            $cacheHeader->removeDirective('no-store');
        }

        $this->isRequestCacheable = false;

        return $result;
    }
}
