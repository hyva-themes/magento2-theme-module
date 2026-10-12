<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
use Magento\Store\Model\ScopeInterface;

class ProductPage implements ArgumentInterface, IdentityInterface
{
    /** Recently Viewed lifetime */
    public const XML_LIFETIME_PATH = "catalog/recently_products/recently_viewed_lifetime";

    /**
     * Flag if recently viewed product data should be fetched via graphql or stored completely in local storage
     *
     * @deprecated The recently viewed products are always read from the browser storage, so the setting this
     *             points at has no effect in Hyva Theme.
     * @see \Hyva\Theme\ViewModel\RecentlyViewedProducts::XML_VIEWED_PRODUCTS_SYNC_BACKEND_PATH
     */
    public const XML_VIEWED_PRODUCTS_SYNC_BACKEND_PATH = 'catalog/recently_products/synchronize_with_backend';

    /** Flag if product URLs contain the category path */
    public const XML_PRODUCT_URL_USE_CATEGORY_PATH = 'catalog/seo/product_use_categories';

    /** Flag if the description may be used when a product has no short description */
    public const XML_SHORT_DESCRIPTION_FALLBACK_PATH = 'hyva_theme_catalog/product_details/short_description_fallback';

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
        ImageFactory $productImageFactory
    ) {
        $this->coreRegistry           = $registry;
        $this->priceCurrency          = $priceCurrency;
        $this->cartHelper             = $cartHelper;
        $this->productOutputHelper    = $productOutputHelper;
        $this->scopeConfigInterface   = $scopeConfigInterface;
        $this->productImageFactory    = $productImageFactory;
    }

    public function isProductUrlIncludesCategory(): bool
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return (bool) $this->scopeConfigInterface->getValue(self::XML_PRODUCT_URL_USE_CATEGORY_PATH, $storeScope);
    }

    public function isShortDescriptionFallbackEnabled(): bool
    {
        return $this->scopeConfigInterface->isSetFlag(
            self::XML_SHORT_DESCRIPTION_FALLBACK_PATH,
            ScopeInterface::SCOPE_STORE
        );
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
        bool $stripTags = true,
        ?bool $descriptionFallback = null
    ): string {
        return $this->getShortDescriptionForProduct($this->getProduct(), $excerpt, $stripTags, $descriptionFallback);
    }

    public function getShortDescriptionForProduct(
        Product $product,
        bool $excerpt = true,
        bool $stripTags = true,
        ?bool $descriptionFallback = null
    ): string {
        $result = "";
        $useFallback = $descriptionFallback ?? $this->isShortDescriptionFallbackEnabled();

        if ($shortDescription = $product->getShortDescription()) {
            $shortDescription = $excerpt ? $this->excerptFromDescription($shortDescription) : $shortDescription;
            $result = $this->productAttributeHtml($product, $shortDescription, 'short_description');
        } elseif ($useFallback && ($description = $product->getDescription())) {
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
        if (stripos($description, '<p>') !== false &&
            $paragraphs = preg_split('#</p><p>|<p>|</p>#i', $description, -1, PREG_SPLIT_NO_EMPTY)
        ) {
            return $paragraphs[0];
        }
        // otherwise, take the first sentence, keeping its own punctuation
        // a period not followed by whitespace, like in "2.5 kg", does not end the sentence
        $parts = preg_split('/([.!?])(?=\s|$)/', trim($description), 2, PREG_SPLIT_DELIM_CAPTURE);
        return $parts[0] . ($parts[1] ?? '.');
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

    public function format($value, $includeContainer = true): string
    {
        return $this->priceCurrency->format($value, $includeContainer);
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
    public function getImage(?Product $product = null, ?string $imageId = null, ?array $attributes = null): Image
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
