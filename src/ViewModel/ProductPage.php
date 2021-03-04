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
use Magento\Checkout\Helper\Cart;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Output;

class ProductPage implements ArgumentInterface
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
     * @var Cart
     */
    protected $cartHelper;

    /**
     * @var Output
     */
    protected $productOutputHelper;

    /**
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param Cart $cartHelper
     * @param Output $productOutputHelper
     */
    public function __construct(
        Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        Cart $cartHelper,
        Output $productOutputHelper
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

    public function getShortDescription(): string
    {
        $product = $this->getProduct();

        if ($shortDescription = $product->getShortDescription()) {
            $excerpt = $this->excerptFromDescription($shortDescription);
            return $this->productAttributeHtml($product, $excerpt, 'short_description');
        }

        if ($description = $product->getDescription()) {
            $excerpt = $this->excerptFromDescription($description);
            return $this->productAttributeHtml($product, $excerpt, 'description');
        }

        return "";
    }

    protected function excerptFromDescription(string $description): string
    {
        // if we have at least one <p></p>, take the first one as excerpt
        if ($paragraphs = preg_split('#</p><p>|<p>|</p>#i', $description, -1, PREG_SPLIT_NO_EMPTY)) {
            return strip_tags($paragraphs[0]);
        }
        // otherwise, take the first sentence
        return explode('.', strip_tags($description))[0] . '.';
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
            'code' => $currency->getCurrencyCode(),
            'symbol' => $currency->getCurrencySymbol()
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
}
