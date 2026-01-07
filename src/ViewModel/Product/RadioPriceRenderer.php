<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Product;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option;
use Magento\Bundle\Model\Selection;
use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class RadioPriceRenderer implements ArgumentInterface
{
    /**
     * @var Option
     */
    private $option;

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
