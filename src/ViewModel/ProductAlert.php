<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Registry;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\ProductAlert\Helper\Data as ProductAlertHelper;
use Magento\ProductAlert\Model\Observer as ProductAlertObserver;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;

class ProductAlert implements ArgumentInterface
{
    /**
     * @var null|Product $product
     */
    protected $product = null;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ProductRegistry $coreRegistry
     */
    private $coreRegistry;

    /**
     * @var UrlHelper
     */
    private $urlHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var ProductAlertHelper
     */
    private $productAlertHelper;

    private $productRegistryViewModel;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ProductRegistry $coreRegistry
     * @param UrlHelper $urlHelper
     * @param UrlInterface $urlBuilder
     * @param ProductAlertHelper $productAlertHelper
     * @param ProductRegistry $productRegistryViewModel
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Registry $coreRegistry,
        UrlHelper $urlHelper,
        UrlInterface $urlBuilder,
        ProductAlertHelper $productAlertHelper,
        ProductRegistry $productRegistryViewModel
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->coreRegistry = $coreRegistry;
        $this->urlHelper = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->productAlertHelper = $productAlertHelper;
        $this->productRegistryViewModel = $productRegistryViewModel;
    }

    /**
     * @return ProductInterface|bool
     */
    protected function getProduct()
    {
        if ($this->product && $this->product->getId()) {
            return $this->product;
        }

        $product = $this->productRegistryViewModel->get();
        if ($product && $product->getId()) {
            return $product;
        }
        return false;
    }

    public function setProduct(Product $product): ProductAlert
    {
        $this->product = $product;
        return $this;
    }

    public function getSaveUrl(string $type): string
    {
        return $this->urlBuilder->getUrl(
            'productalert/add/' . $type,
            [
                'product_id' => $this->getProduct()->getId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl()
            ]
        );
    }

    public function showStockAlert(): bool
    {
        return $this->getProduct() &&
            !$this->getProduct()->isAvailable() &&
            $this->scopeConfig->isSetFlag(
                ProductAlertObserver::XML_PATH_STOCK_ALLOW,
                StoreScopeInterface::SCOPE_STORE
            );
    }

    public function showPriceAlert(): bool
    {
        return $this->getProduct() &&
            $this->getProduct()->isSalable()
            && $this->scopeConfig->isSetFlag(
                ProductAlertObserver::XML_PATH_PRICE_ALLOW,
                StoreScopeInterface::SCOPE_STORE
            );
    }
}
