<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class ProductCompare implements ArgumentInterface
{
    private const CONFIG_PATH_SHOW_COMPARE_IN_PRODUCT_LIST = 'catalog/frontend/show_add_to_compare_in_list';
    private const CONFIG_PATH_SHOW_COMPARE_SIDEBAR_IN_LIST = 'catalog/frontend/show_sidebar_in_list';
    private const CONFIG_PATH_SHOW_COMPARE_ON_PRODUCT_PAGE = 'catalog/frontend/show_add_to_compare_on_product_page';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function showInProductList(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_SHOW_COMPARE_IN_PRODUCT_LIST,
            ScopeInterface::SCOPE_STORES
        );
    }

    public function showCompareSidebar(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_SHOW_COMPARE_SIDEBAR_IN_LIST,
            ScopeInterface::SCOPE_STORES
        );
    }

    public function showOnProductPage(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_SHOW_COMPARE_ON_PRODUCT_PAGE,
            ScopeInterface::SCOPE_STORES
        );
    }
}
