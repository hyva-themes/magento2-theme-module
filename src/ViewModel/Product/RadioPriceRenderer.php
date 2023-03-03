<?php

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Product;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option;
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
     * @param $selection
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
