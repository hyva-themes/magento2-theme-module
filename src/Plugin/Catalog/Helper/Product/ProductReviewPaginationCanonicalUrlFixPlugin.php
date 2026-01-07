<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Catalog\Helper\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product\View as ProductViewHelper;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\PropertyGroup as PageAssetPropertyGroup;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Store\Model\StoreManagerInterface;

class ProductReviewPaginationCanonicalUrlFixPlugin
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
     * @var HttpRequest
     */
    private $request;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager,
        HttpRequest $request
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
    }

    /**
     * Set value of canonical url to include pagination query parameter
     *
     * @param ProductViewHelper $subject
     * @param ProductViewHelper $result
     * @param ResultPage $resultPage
     * @param int|string $productId
     * @return ProductViewHelper
     */
    public function afterPrepareAndRender(ProductViewHelper $subject, $result, ResultPage $resultPage, $productId)
    {
        $pageNumber = $this->request->getParam('p');

        if ($pageNumber) {
            $this->updateCanonicalUrlToIncludePagination($resultPage, (int) $productId, (int) $pageNumber);
        }

        return $result;
    }

    private function updateCanonicalUrlToIncludePagination(ResultPage $resultPage, int $productId, int $pageNumber): void
    {
        // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        try {
            $product = $this->getProduct($productId);

            $pageConfig = $resultPage->getConfig();

            // Canonical link tags are handled as part of the page assets
            // They are called "groups" because the same asset property could be present more than once
            $pageAssets = $pageConfig->getAssetCollection()->getGroups();

            foreach ($pageAssets as $assetGroup) {
                if ($assetGroup->getProperty('content_type') == 'canonical') {
                    $this->removeAssetGroup($pageConfig, $assetGroup);
                    $this->addCanonicalUrlWithPagination($pageConfig, $product, $pageNumber);
                }
            }
        } catch (NoSuchEntityException $exception) {
            // This would already have been triggered by the plugin target method, so the try/catch block exists only to make the IDE happy
        }
    }

    private function getProduct($productId): ProductInterface
    {
        // This will not load the product again, but rather pull it from the instance cache since the plugin target method already loaded it
        return $this->productRepository->getById($productId, /* editMode */ false, $this->storeManager->getStore()->getId());
    }

    private function removeAssetGroup(PageConfig $pageConfig, PageAssetPropertyGroup $assetGroup): void
    {
        // For canonical link tags this will only be a single iteration for the URL
        foreach (array_keys($assetGroup->getAll()) as $assetName) {
            $pageConfig->getAssetCollection()->remove($assetName);
        }
    }

    private function addCanonicalUrlWithPagination(PageConfig $pageConfig, ProductInterface $product, $pageNumber): void
    {
        $pageConfig->addRemotePageAsset(
            $product->getUrlModel()->getUrl($product, ['_ignore_category' => true, '_query' => ['p' => $pageNumber]]),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
    }
}
