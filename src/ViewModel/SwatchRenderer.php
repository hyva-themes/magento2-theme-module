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
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Swatches\Helper\Data as SwatchHelper;

class SwatchRenderer implements ArgumentInterface
{
    /**
     * @var SwatchHelper
     */
    private $swatchHelper;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @param SwatchHelper $swatchHelper
     */
    public function __construct(SwatchHelper $swatchHelper, HttpRequest $httpRequest, EavConfig $eavConfig)
    {
        $this->swatchHelper = $swatchHelper;
        $this->httpRequest  = $httpRequest;
        $this->eavConfig    = $eavConfig;
    }

    /**
     * @param Attribute $attribute
     */
    public function isSwatchAttribute($attribute): bool
    {
        return $this->swatchHelper->isSwatchAttribute($attribute);
    }

    /**
     * @see \Magento\Swatches\Model\Plugin\ProductImage::getFilterArray
     */
    public function getUsedSwatchFilters(Product $product): array
    {
        if ($product->getTypeId() === Product\Type::TYPE_SIMPLE) {
            return [];
        }

        $requestParams        = $this->httpRequest->getParams();
        $allAttributes        = $this->eavConfig->getEntityAttributes(Product::ENTITY, $product);
        $usedFilterAttributes = [];
        foreach ($requestParams as $code => $value) {
            if (isset($allAttributes[$code])) {
                $attribute = $allAttributes[$code];
                if ($this->canReplaceImageWithSwatch($attribute)) {
                    $usedFilterAttributes[$code] = $value;
                }
            }
        }

        return $usedFilterAttributes;
    }

    /**
     * Check if we can replace original image with swatch image on catalog/category/list page
     *
     * @see \Magento\Swatches\Model\Plugin\ProductImage::canReplaceImageWithSwatch
     */
    private function canReplaceImageWithSwatch($attribute)
    {
        $result = true;
        if (!$this->isSwatchAttribute($attribute)) {
            $result = false;
        }

        if (!$attribute->getUsedInProductListing()
            || !$attribute->getIsFilterable()
            || !$attribute->getData('update_product_preview_image')
        ) {
            $result = false;
        }

        return $result;
    }
}
