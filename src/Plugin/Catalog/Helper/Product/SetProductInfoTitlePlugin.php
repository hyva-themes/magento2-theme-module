<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Catalog\Helper\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product\View as ProductViewHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Store\Model\StoreManagerInterface;

class SetProductInfoTitlePlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        StoreManagerInterface $storeManager
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
    }

    /**
     * Set the product name as page title on the Hyvä product.info.title block
     *
     * Mirrors what \Magento\Catalog\Helper\Product\View::preparePageMetadata does
     * for the page.main.title block.
     *
     * @param ProductViewHelper $subject
     * @param ProductViewHelper $result
     * @param ResultPage $resultPage
     * @param int|string $productId
     * @return ProductViewHelper
     */
    public function afterPrepareAndRender(ProductViewHelper $subject, $result, ResultPage $resultPage, $productId)
    {
        $productInfoTitle = $resultPage->getLayout()->getBlock('product.info.title');
        if (!$productInfoTitle) {
            return $result;
        }

        // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
        try {
            // Pull from repository instance cache — product already loaded by target method
            $storeId = $this->storeManager->getStore()->getId();
            $product = $this->productRepository->getById((int) $productId, false, $storeId);
            $productInfoTitle->setPageTitle($product->getName());
        } catch (NoSuchEntityException $exception) {
            // This would already have been triggered by the plugin target method, so the try/catch block exists only to make the IDE happy
        }

        return $result;
    }
}
