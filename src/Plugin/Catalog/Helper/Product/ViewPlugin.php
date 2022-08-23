<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Catalog\Helper\Product;

use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Catalog\Helper\Product\View;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http;

class ViewPlugin
{   
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Http
     */
    private $request;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        Http $request
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

   /**
     * Reset value of canonical url page after reload review pagination
     *
     * $params can have all values as $params in \Magento\Catalog\Helper\Product - initProduct().
     * Plus following keys:
     *   - 'buy_request' - \Magento\Framework\DataObject holding buyRequest to configure product
     *   - 'specify_options' - boolean, whether to show 'Specify options' message
     *   - 'configure_mode' - boolean, whether we're in Configure-mode to edit product configuration
     * @param View $subject
     * @param Magento\Catalog\Helper\Product\View $result
     * @param \Magento\Framework\View\Result\Page $resultPage
     * @param int $productId
     * @param \Magento\Framework\App\Action\Action $controller
     * @param null|\Magento\Framework\DataObject $params
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Magento\Catalog\Helper\Product\View
     */
    public function afterPrepareAndRender(View $subject, $result, ResultPage $resultPage, $productId, $controller, $params = null)
    {
        try {
            $product = $this->productRepository->getById($productId, false, $this->storeManager->getStore()->getId());
            /** @var \Magento\Framework\View\Page\Config $pageConfig */
            $pageConfig = $resultPage->getConfig();
            $assets = $pageConfig->getAssetCollection()->getGroups();

            foreach ($assets as $asset) {
                if ($asset->getProperty('content_type') == 'canonical') {
                    $url = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true]);
                    $pageConfig->getAssetCollection()->remove($url);
                    $queryParams = [
                        'p' => $this->request->getParam('p')
                    ];
                    $newUrl = $product->getUrlModel()->getUrl($product, ['_ignore_category' => true, '_query' => $queryParams]);
                    $pageConfig->addRemotePageAsset(
                        $newUrl,
                        'canonical',
                        ['attributes' => ['rel' => 'canonical']]
                    );
                }
            }
            return $result;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
