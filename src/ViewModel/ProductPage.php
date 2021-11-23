<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Block\Product\Image;
use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\Phrase;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Output as ProductOutputHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProductPage implements ArgumentInterface, IdentityInterface
{
    /** Recently Viewed lifetime */
    const XML_LIFETIME_PATH = "catalog/recently_products/recently_viewed_lifetime";

    /** Flag if recently viewed product data should be fetched via graphql or stored completely in local storage */
    const XML_VIEWED_PRODUCTS_SYNC_BACKEND_PATH = 'catalog/recently_products/synchronize_with_backend';

    /** Flag if product URLs contain the category path */
    const XML_PRODUCT_URL_USE_CATEGORY_PATH = 'catalog/seo/product_use_categories';

    /**
     * @var Product
     */
    protected $_product = null;

    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var CartHelper
     */
    protected $cartHelper;

    /**
     * @var ProductOutputHelper
     */
    protected $productOutputHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfigInterface;

    /**
     * @var ImageFactory
     */
    private $productImageFactory;

    /**
     * @var RecentlyViewedProducts
     */
    private $recentlyViewedProducts;

    /**
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param CartHelper $cartHelper
     * @param ProductOutputHelper $productOutputHelper
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param ImageFactory $productImageFactory
     */
    public function __construct(
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        CartHelper $cartHelper,
        ProductOutputHelper $productOutputHelper,
        ScopeConfigInterface $scopeConfigInterface,
        ImageFactory $productImageFactory,
        RecentlyViewedProducts $recentlyViewedProducts
    ) {
        $this->coreRegistry           = $registry;
        $this->priceCurrency          = $priceCurrency;
        $this->cartHelper             = $cartHelper;
        $this->productOutputHelper    = $productOutputHelper;
        $this->scopeConfigInterface   = $scopeConfigInterface;
        $this->productImageFactory    = $productImageFactory;
        $this->recentlyViewedProducts = $recentlyViewedProducts;
    }

    /**
     * @deprecated
     * @see \Hyva\Theme\ViewModel\RecentlyViewedProducts::getRecentlyViewedLifeTime
     */
    public function getRecentlyViewedLifeTime()
    {
        return $this->recentlyViewedProducts->getRecentlyViewedLifeTime();
    }

    /**
     * @deprecated
     * @see \Hyva\Theme\ViewModel\RecentlyViewedProducts::isFetchRecentlyViewedEnabled
     */
    public function isFetchRecentlyViewedEnabled(): bool
    {
        return $this->recentlyViewedProducts->isFetchRecentlyViewedEnabled();
    }

    public function isProductUrlIncludesCategory(): bool
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return (bool) $this->scopeConfigInterface->getValue(self::XML_PRODUCT_URL_USE_CATEGORY_PATH, $storeScope);
    }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->_product) {
            $this->_product = $this->coreRegistry->registry('product');
        }
        return $this->_product;
    }

    public function getShortDescription(
        bool $excerpt = true,
        bool $stripTags = true
    ): string {
        $product = $this->getProduct();
        $result = "";

        if ($shortDescription = $product->getShortDescription()) {
            $shortDescription = $excerpt ? $this->excerptFromDescription($shortDescription) : $shortDescription;
            $result = $this->productAttributeHtml($product, $shortDescription, 'short_description');
        } elseif ($description = $product->getDescription()) {
            $description = $excerpt ? $this->excerptFromDescription($description) : $description;
            $result = $this->productAttributeHtml($product, $description, 'description');
        }

        return $stripTags ? strip_tags($this->stripStyles($result)) : $result;
    }

    protected function stripStyles(string $html): string
    {
        return preg_replace('#<style>.+</style>#Usi', '', $html);
    }

    protected function excerptFromDescription(string $description): string
    {
        // if we have at least one <p></p>, take the first one as excerpt
        if ($paragraphs = preg_split('#</p><p>|<p>|</p>#i', $description, -1, PREG_SPLIT_NO_EMPTY)) {
            return $paragraphs[0];
        }
        // otherwise, take the first sentence
        return explode('.', $description)[0] . '.';
    }

    /**
     * Retrieve url for direct adding product to cart
     *
     * @param Product $product
     * @param array $additional
     * @return string
     */
    public function getAddToCartUrl($product, $additional = [])
    {
        return $this->cartHelper->getAddUrl($product, $additional);
    }

    /**
     * @deprecated Use `$this->getCurrencyData()['code']` instead.
     */
    public function getCurrency(): string
    {
        return $this->getCurrencyData()['code'];
    }

    public function getCurrencyData(): array
    {
        $currency = $this->priceCurrency->getCurrency();
        return [
            'code'   => $currency->getCurrencyCode(),
            'symbol' => $currency->getCurrencySymbol(),
        ];
    }

    public function format($value): string
    {
        return $this->priceCurrency->format($value);
    }

    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }

    /**
     * @param string|Phrase $attributeHtml
     * @param string $attributeName
     * @return mixed
     */
    public function productAttributeHtml(Product $product, $attributeHtml, $attributeName)
    {
        return $this->productOutputHelper->productAttribute($product, $attributeHtml, $attributeName);
    }

    /**
     * @param Product|null $product
     * @param string|null $imageId
     * @param string[]|null $attributes
     */
    public function getImage(Product $product = null, string $imageId = null, array $attributes = null): Image
    {
        return $this->productImageFactory->create($product, $imageId, $attributes);
    }

    public function getIdentities()
    {
        return isset($this->_product)
            ? $this->_product->getIdentities()
            : [];
    }
}
