<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\QuoteGraphQL;

use Magento\QuoteGraphQl\Model\CartItem\DataProvider\CustomizableOption;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\QuoteGraphQl\Model\CartItem\DataProvider\CustomizableOptionValueInterface;

/**
 * Custom Option Data provider
 */
class CustomizableOptionPlugin
{
    /**
     * @var CustomizableOptionValueInterface
     */
    private $customizableOptionValue;

    /**
     * @param CustomizableOptionValueInterface $customOptionValueDataProvider
     */
    public function __construct(
        CustomizableOptionValueInterface $customOptionValueDataProvider
    ) {
        $this->customizableOptionValue = $customOptionValueDataProvider;
    }

    /**
     * @param CustomizableOption $customizableOption
     * @param $result
     * @param QuoteItem $cartItem
     * @param int $optionId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetData(
        CustomizableOption $customizableOption,
        $result,
        QuoteItem $cartItem,
        int $optionId
    ): array {
        $product = $cartItem->getProduct();
        $option = $product->getOptionById($optionId);

        if (!$option) {
            return [];
        }

        $selectedOption = $cartItem->getOptionByCode('option_' . $option->getId());

        $selectedOptionValueData = $this->customizableOptionValue->getData(
            $cartItem,
            $option,
            $selectedOption
        );

        return [
            'id'          => $option->getId(),
            'label'       => $option->getTitle(),
            'type'        => $option->getType(),
            'values'      => $selectedOptionValueData,
            'has_file'    => $selectedOption->getData('has_file'),
            'sort_order'  => $option->getSortOrder(),
            'is_required' => $option->getIsRequire(),
        ];
    }
}
