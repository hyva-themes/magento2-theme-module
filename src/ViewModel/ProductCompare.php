<?php

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class ProductCompare implements ArgumentInterface
{
    private const CONFIG_PATH_SHOW_COMPARE_IN_PRODUCT_LIST = 'catalog/storefront/show_add_to_compare_in_list';
    private const CONFIG_PATH_SHOW_COMPARE_ON_PRODUCT_PAGE = 'catalog/storefront/show_add_to_compare_in_list';

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
            ScopeInterface::SCOPE_STORES,
            null
        );
    }

    public function showOnProductPage(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_SHOW_COMPARE_ON_PRODUCT_PAGE,
            ScopeInterface::SCOPE_STORES,
            null
        );
    }
}

