<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Helper\Output as ProductOutputHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductAttributes implements ArgumentInterface, IdentityInterface
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
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var ProductOutputHelper
     */
    private $productOutputHelper;

    /**
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Registry $registry,
        ProductResource $productResource,
        PriceCurrencyInterface $priceCurrency,
        ProductOutputHelper $productOutputHelper
    ) {
        $this->coreRegistry        = $registry;
        $this->priceCurrency       = $priceCurrency;
        $this->productResource     = $productResource;
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

    public function getAttributeFromLayoutConfig($config)
    {
        $product   = $this->getProduct();
        $code      = $config['code'];
        $attribute = $this->productResource->getAttribute($code);

        if (!$attribute) {
            return [];
        }

        $call     = $config['call'] ?? 'default';
        $label    = $config['label'] ?? 'default';
        $cssClass = $config['css_class'] ?? 'attribute';

        $defaultData = $this->getAttributeData($attribute, $product);

        return [
            'label'     => ($label && $label === 'default') ? $defaultData['label'] : $label,
            'value'     => ($call && $call !== 'default') ? $product->{$call}() : $defaultData['value'],
            'code'      => $code,
            'css_class' => $cssClass,
        ];
    }

    /**
     * $excludeAttr is optional array of attribute codes to exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllVisibleAttributes(array $excludeAttr = [])
    {
        $data       = [];
        $product    = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($this->isVisibleOnFrontend($attribute, $excludeAttr)) {
                $attributeData = $this->getAttributeData($attribute, $product);
                if ($attributeData && $attributeData['value']) {
                    $data[$attribute->getAttributeCode()] = $attributeData;
                }
            }
        }
        return $data;
    }

    /**
     * @param AbstractAttribute $attribute
     * @param Product $product
     * @return string[]
     */
    public function getAttributeData($attribute, $product)
    {
        $code  = $attribute->getAttributeCode();
        $rawValue = $attribute->getFrontend()->getValue($product);

        if ($attribute->getFrontendInput() == 'price' && is_string($rawValue)) {
            $value = $this->priceCurrency->convertAndFormat($rawValue);
        } else {
            $value = $this->productOutputHelper->productAttribute($product, $rawValue, $code);
        }

        return [
            'label' => $attribute->getStoreLabel(),
            'value' => (string) $value,
            'code'  => $code,
        ];
    }

    /**
     * Determine if we should display the attribute on the front-end
     *
     * @param AbstractAttribute $attribute
     * @param array $excludeAttr
     * @return bool
     * @since 103.0.0
     */
    protected function isVisibleOnFrontend(
        AbstractAttribute $attribute,
        array $excludeAttr
    ) {
        return (
            $attribute->getIsVisibleOnFront()
            && !in_array($attribute->getAttributeCode(), $excludeAttr)
        );
    }

    public function getIdentities()
    {
        return ($product = $this->getProduct())
            ? $product->getIdentities()
            : [];
    }
}
