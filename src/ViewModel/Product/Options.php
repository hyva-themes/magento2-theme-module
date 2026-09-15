<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Product;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableBlock;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute as ConfigurableAttribute;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Options implements ArgumentInterface
{
    /**
     * Return only the attribute options at least one allowed child product carries.
     *
     * $attribute->getOptions() lists every option linked to the configurable, including those whose
     * only child products are disabled or not assigned to this website. Those can never be selected,
     * so they should not be rendered at all.
     *
     * @param ConfigurableBlock $block
     * @param ConfigurableAttribute $attribute
     * @return array[]
     */
    public function getEnabledAttributeOptions(
        ConfigurableBlock $block,
        ConfigurableAttribute $attribute
    ): array {
        $attributeCode = $attribute->getProductAttribute()->getAttributeCode();

        // Listing blocks render every product in the list, but cache allow_products on themselves,
        // so drop the previous product's list rather than relying on getJsonConfig() to have done it.
        $block->unsetData('allow_products');

        $enabledValues = [];
        foreach ($block->getAllowProducts() as $childProduct) {
            $enabledValues[(string) $childProduct->getData($attributeCode)] = true;
        }

        return array_values(array_filter(
            $attribute->getOptions() ?? [],
            static fn (array $option): bool => isset($enabledValues[(string) $option['value_index']])
        ));
    }
}
