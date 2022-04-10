<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class ProductListItem implements ArgumentInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var ProductPage
     */
    private $productViewModel;

    /**
     * @var CurrentCategory
     */
    private $currentCategory;

    /**
     * @var BlockCache
     */
    private $blockCache;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    public function __construct(
        LayoutInterface $layout,
        ProductPage $productViewModel,
        CurrentCategory $currentCategory,
        BlockCache $blockCache,
        CustomerSession $customerSession
    ) {
        $this->layout           = $layout;
        $this->productViewModel = $productViewModel;
        $this->currentCategory  = $currentCategory;
        $this->blockCache       = $blockCache;
        $this->customerSession = $customerSession;
    }

    public function getProductPriceHtml(
        Product $product
    ) {
        $priceType = FinalPrice::PRICE_CODE;

        $arguments = [
            'include_container'     => true,
            'display_minimal_price' => true,
            'list_category_page'    => true,
            'zone'                  => Render::ZONE_ITEM_LIST,
        ];

        /** @var Render $priceRender */
        $priceRender = $this->layout->getBlock('product.price.render.default');
        $price       = '';

        if ($priceRender) {
            $price = $priceRender->render($priceType, $product, $arguments);
        }
        return $price;
    }

    private function getCurrencyCode(): string
    {
        return $this->productViewModel->getCurrencyData()['code'];
    }

    private function isCategoryInProductUrl(): bool
    {
        return $this->productViewModel->isProductUrlIncludesCategory() && $this->currentCategory->exists();
    }

    public function getItemCacheKeyInfo(
        Product $product,
        Template $block,
        string $viewMode,
        string $templateType
    ): array {
        return [
            $product->getId(),
            $viewMode,
            $templateType,
            $block->getTemplate(),
            $product->getStoreId(),
            $this->getCurrencyCode(),
            (int) $block->getData('hideDetails'),
            (int) $block->getData('hide_rating_summary'),
            $this->isCategoryInProductUrl()
                ? $this->currentCategory->get()->getId()
                : '0',
            (int) $this->customerSession->getCustomerGroupId()
        ];
    }

    public function getItemHtmlWithRenderer(
        AbstractBlock $itemRendererBlock,
        Product $product,
        Template $parentBlock,
        string $viewMode,
        string $templateType,
        string $imageDisplayArea,
        bool $showDescription
    ): string {
        // Careful! Temporal coupling!
        // First the values on the block need to be set, then the cache key info array can be created.

        $itemRendererBlock->setData('product', $product)
                          ->setData('view_mode', $viewMode)
                          ->setData('image_display_area', $imageDisplayArea)
                          ->setData('show_description', $showDescription)
                          ->setData('position', $parentBlock->getPositioned())
                          ->setData('pos', $parentBlock->getPositioned())
                          ->setData('template_type', $templateType)
                          ->setData('cache_lifetime', 3600)
                          ->setData('cache_tags', $product->getIdentities());

        $itemCacheKeyInfo = $this->getItemCacheKeyInfo($product, $parentBlock, $viewMode, $templateType);
        $itemRendererBlock->setData('cache_key', $this->blockCache->hashCacheKeyInfo($itemCacheKeyInfo));

        return $itemRendererBlock->toHtml();
    }

    public function getItemHtml(
        Product $product,
        Template $parentBlock,
        string $viewMode,
        string $templateType,
        string $imageDisplayArea,
        bool $showDescription
    ): string {
        /** @var Template $itemRendererBlock */
        $itemRendererBlock = $this->layout->getBlock('product_list_item');
        return $this->getItemHtmlWithRenderer(
            $itemRendererBlock,
            $product,
            $parentBlock,
            $viewMode,
            $templateType,
            $imageDisplayArea,
            $showDescription
        );
    }
}
