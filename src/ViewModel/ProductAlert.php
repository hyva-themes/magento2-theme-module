<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\ActionInterface;
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
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ProductAlertHelper
     */
    protected $productAlertHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param UrlHelper $urlHelper
     * @param UrlInterface $urlBuilder
     * @param ProductAlertHelper $productAlertHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlHelper $urlHelper,
        UrlInterface $urlBuilder,
        ProductAlertHelper $productAlertHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlHelper = $urlHelper;
        $this->urlBuilder = $urlBuilder;
        $this->productAlertHelper = $productAlertHelper;
    }

    public function getSaveUrl(ProductInterface $product, string $type): string
    {
        return $this->urlBuilder->getUrl(
            'productalert/add/' . $type,
            [
                'product_id' => $product->getId(),
                ActionInterface::PARAM_NAME_URL_ENCODED => $this->urlHelper->getEncodedUrl()
            ]
        );
    }

    public function showStockAlert(ProductInterface $product): bool
    {
        return $product &&
            !$product->isAvailable() &&
            $this->scopeConfig->isSetFlag(
                ProductAlertObserver::XML_PATH_STOCK_ALLOW,
                StoreScopeInterface::SCOPE_STORE
            );
    }

    public function showPriceAlert(ProductInterface $product): bool
    {
        return $product &&
            $product->isSalable()
            && $this->scopeConfig->isSetFlag(
                ProductAlertObserver::XML_PATH_PRICE_ALLOW,
                StoreScopeInterface::SCOPE_STORE
            );
    }
}
