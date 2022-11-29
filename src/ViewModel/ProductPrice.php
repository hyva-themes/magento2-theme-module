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
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\PriceInfoInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Tax\Model\Config;

class ProductPrice implements ArgumentInterface
{

    /**
     * @var Product
     */
    protected $product = null;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var Config
     */
    protected $taxConfig;

    /**
     * @var CurrentProduct
     */
    private $currentProduct;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        Config $taxConfig,
        CurrentProduct $currentProduct
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->taxConfig = $taxConfig;
        $this->currentProduct = $currentProduct;
    }

    public function getPriceValueInclTax(string $priceType, Product $product): float
    {
        // Cast in case no price is set and $amount is null or a false
        return (float) $this->getPrice($priceType, $product)->getAmount()->getValue();
    }

    public function getPriceValueExclTax(string $priceType, Product $product): float
    {
        // Cast in case no price is set and $amount is null or a false
        return (float) $this->getPrice($priceType, $product)->getAmount()->getBaseAmount();
    }

    /**
     * Return the price incl. or excl. tax depending on the system settings.
     *
     * @param string $priceType
     * @param Product|null $product
     * @return float
     */
    public function getPriceValue(string $priceType, Product $product = null): float
    {
        // Cast in case no price is set and $amount is null or a false
        return (float) $this->displayPriceIncludingTax()
            ? $this->getPriceValueInclTax($priceType, $product ?? $this->getProduct())
            : $this->getPriceValueExclTax($priceType, $product ?? $this->getProduct());
    }

    public function getPrice(string $priceType, Product $product = null): PriceInterface
    {
        return $this->getPriceInfo($product)->getPrice($priceType);
    }

    protected function getPriceInfo(Product $product = null): PriceInfoInterface
    {
        $product = $product ?? $this->getProduct();

        return $product->getPriceInfo();
    }

    /**
     * @return Product|null
     * @deprecated Pass the product instance to price methods instead
     */
    protected function getProduct(): ?Product
    {
        if (!$this->product) {
            $this->currentProduct->get();
        }
        return $this->product;
    }

    /**
     * @param Product $product
     * @deprecated Pass the $product instance as a second argument to price methods instead
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    public function displayPriceIncludingTax(): bool
    {
        return $this->getPriceDisplayType() == Config::DISPLAY_TYPE_INCLUDING_TAX || $this->displayPriceInclAndExclTax();
    }

    public function displayPriceInclAndExclTax(): bool
    {
        return $this->getPriceDisplayType() === Config::DISPLAY_TYPE_BOTH;
    }

    /**
     * Get product price display type
     *  1 - Excluding tax
     *  2 - Including tax
     *  3 - Both
     *
     * @param null|int|string|Store $store
     * @return int
     */
    public function getPriceDisplayType($store = null): int
    {
        return $this->taxConfig->getPriceDisplayType($store);
    }

    public function currency($value, $format = true, $includeContainer = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat($value, $includeContainer)
            : $this->priceCurrency->convert($value);
    }

    public function format($value, $includeContainer = true)
    {
        return $this->priceCurrency->format($value, $includeContainer);
    }

    public function getTierPrices($priceType, Product $product = null)
    {
        $tierPrices = $this->getPrice($priceType, $product)->getTierPriceList();
        $displayTax = $this->displayPriceIncludingTax();

        // overrides the 'website_price' key with the required price including or excluding tax
        return array_map(
            function ($a, $b) use ($displayTax) {
                $b['price_incl_tax'] = $b['price']->getValue();
                $b['price_excl_tax'] = $b['price']->getBaseAmount();
                $b['website_price'] = $displayTax ? $b['price']->getValue() : $b['price']->getBaseAmount(); // keep for backwards compatibility
                return $b;
            },
            array_keys($tierPrices),
            array_values($tierPrices)
        );
    }

    /**
     * Return the price incl. or excl. tax depending on the system settings.
     *
     * @param Option|Value $option
     * @param string $priceType
     * @param Product|null $product
     * @return float|mixed
     */
    public function getCustomOptionPrice($option, string $priceType, Product $product = null)
    {
        return $this->displayPriceIncludingTax()
            ? $this->getCustomOptionPriceInclTax($option, $priceType, $product)
            : $this->getCustomOptionPriceExclTax($option, $priceType, $product);
    }

    /**
     * @param Value|Option $option
     * @param string $priceType
     * @param Product|null $product
     * @return float|mixed
     */
    public function getCustomOptionPriceInclTax($option, string $priceType, Product $product = null)
    {
        return $this->calcCustomOptionPrice($option, $priceType, $product ?? $this->getProduct(), true);
    }

    /**
     * @param Value|Option $option
     * @param string $priceType
     * @param Product|null $product
     * @return float|mixed
     */
    public function getCustomOptionPriceExclTax($option, string $priceType, Product $product = null)
    {
        return $this->calcCustomOptionPrice($option, $priceType, $product ?? $this->getProduct(), false);
    }

    /**
     * @param Value|Option $option
     * @param string $priceType
     * @param Product $product
     * @param bool $withTax
     * @return float
     */
    private function calcCustomOptionPrice($option, string $priceType, Product $product, bool $withTax)
    {
        if ($option->getPriceType() === 'percent') {
            return $option->getPrice();
        }

        $customOptionPrice = $this->getPrice($priceType, $product);
        $context = [CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG => true];
        $optionPrice = $customOptionPrice->getCustomAmount($option->getPrice(), !$withTax, $context);

        return $optionPrice->getValue($withTax ? null : 'tax');
    }
}
