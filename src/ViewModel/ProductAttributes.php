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
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductAttributes implements ArgumentInterface
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
    private $priceCurrency;

    /**
     * @param Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Registry $registry,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->coreRegistry = $registry;
        $this->priceCurrency = $priceCurrency;
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
        $product = $this->getProduct();
        $code = $config['code'];
        $attribute = $product->getResource()->getAttribute($code);

        if (!$attribute) {
            return [];
        }

        $call = $config['call'] ?: 'default';
        $label = $config['label'] ?: 'default';
        $cssClass = $config['css_class'] ?: 'attribute';

        $defaultData = $this->getAttributeData($attribute, $product);

        return [
            'label' => ($label === 'default') ? $defaultData['label'] : $label,
            'value' => ($call !== 'default') ? $product->{$call}() : $defaultData['value'],
            'code' => $code,
            'css_class' => $cssClass
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
        $data = [];
        $product = $this->getProduct();
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

    public function getAttributeData($attribute, $product)
    {
        $value = $attribute->getFrontend()->getValue($product);

        if ($value instanceof Phrase) {
            $value = (string)$value;
        } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
            $value = $this->priceCurrency->convertAndFormat($value);
        }

        return [
            'label' => $attribute->getStoreLabel(),
            'value' => $value,
            'code' => $attribute->getAttributeCode(),
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
}
