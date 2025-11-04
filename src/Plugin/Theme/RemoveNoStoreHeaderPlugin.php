<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Laminas\Http\Header\CacheControl;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RemoveNoStoreHeaderPlugin
{
    private const CONFIG_PATH_BFCACHE = 'system/full_page_cache/bfcache';
    private bool $isCacheable = false;

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_BFCACHE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function beforeSetNoCacheHeaders(HttpResponse $subject): void
    {
        /** @var CacheControl $cacheHeader */
        $cacheHeader = $subject->getHeader('Cache-Control');

        if (!$this->isEnabled() || !$cacheHeader) {
            return;
        }

        $hasCacheHeaderValue = (bool) preg_match('/public.*s-maxage=(\d+)/', $cacheHeader->getFieldValue());
        $this->isCacheable = $hasCacheHeaderValue;
    }

    public function afterSetNoCacheHeaders(HttpResponse $subject): void
    {
        /** @var CacheControl $cacheHeader */
        $cacheHeader = $subject->getHeader('Cache-Control');

        if (!$this->isEnabled() || !$cacheHeader) {
            return;
        }

        if ($this->isCacheable) {
            $cacheHeader->removeDirective('no-store');
        }

        $this->isCacheable = false;
    }
}
