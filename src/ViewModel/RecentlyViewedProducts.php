<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface as ConfigScope;

// phpcs:disable Generic.Files.LineLength.TooLong

class RecentlyViewedProducts implements ArgumentInterface
{
    /* Recently Viewed lifetime */
    public const XML_LIFETIME_PATH = "catalog/recently_products/recently_viewed_lifetime";

    /* Flag if recently viewed product data should be fetched via graphql or stored completely in local storage */
    public const XML_VIEWED_PRODUCTS_SYNC_BACKEND_PATH = 'catalog/recently_products/synchronize_with_backend';

    /* Flag if recently viewed product data should be collected */
    public const XML_VIEWED_PRODUCTS_ENABLED = 'catalog/recently_products/recently_viewed_enabled';

    /* Flag if the recently viewed product widget should be displayed on product detail pages */
    public const XML_VIEWED_PRODUCTS_SHOW_ON_PDP = 'catalog/recently_products/show_on_pdp';

    /* Flag if the recently viewed product widget should be displayed on product list pages */
    public const XML_VIEWED_PRODUCTS_SHOW_ON_PLP = 'catalog/recently_products/show_on_plp';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isRecentlyViewedProductsEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_VIEWED_PRODUCTS_ENABLED, ConfigScope::SCOPE_STORE);
    }

    public function isDisplayOnProductDetailPageEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_VIEWED_PRODUCTS_SHOW_ON_PDP, ConfigScope::SCOPE_STORE);
    }

    public function isDisplayOnProductListingPageEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_VIEWED_PRODUCTS_SHOW_ON_PLP, ConfigScope::SCOPE_STORE);
    }

    public function isFetchRecentlyViewedEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(self::XML_VIEWED_PRODUCTS_SYNC_BACKEND_PATH, ConfigScope::SCOPE_STORE);
    }

    public function getRecentlyViewedLifeTime()
    {
        return $this->scopeConfig->getValue(self::XML_LIFETIME_PATH, ConfigScope::SCOPE_STORE);
    }
}
