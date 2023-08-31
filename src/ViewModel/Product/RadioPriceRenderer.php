<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Product;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class RadioPriceRenderer implements ArgumentInterface
{
    private Option $option;

    public function __construct(
        Option $option
    ) {
        $this->option = $option;
    }

    /**
     * @param Product|Selection $selection
     * @return string
     */
    public function getSelectionTitlePrice($selection): string
    {
        return sprintf(
            '%s%s%s',
            $selection->getName(),
            ' &nbsp; +',
            $this->option->renderPriceString($selection, false)
        );
    }
}
