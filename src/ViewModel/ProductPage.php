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
use Magento\Checkout\Helper\Cart as CartHelper;
use Magento\Framework\Phrase;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Output as ProductOutputHelper;

class ProductPage implements ArgumentInterface, IdentityInterface
{
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
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param CartHelper $cartHelper
     * @param ProductOutputHelper $productOutputHelper
     */
    public function __construct(
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        CartHelper $cartHelper,
        ProductOutputHelper $productOutputHelper
    ) {
        $this->coreRegistry = $registry;
        $this->priceCurrency = $priceCurrency;
        $this->cartHelper = $cartHelper;
        $this->productOutputHelper = $productOutputHelper;
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

        return $stripTags ? strip_tags($result) : $result;
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

    public function getIdentities()
    {
        return isset($this->_product)
            ? $this->_product->getIdentities()
            : [];
    }
}
