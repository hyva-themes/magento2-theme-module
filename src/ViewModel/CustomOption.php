<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Block\Product\View\Options\Type\Select\CheckableFactory;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomOption implements ArgumentInterface
{
    /**
     * @var string
     */
    protected $checkableTemplate = 'Magento_Catalog::product/composite/fieldset/options/view/checkable.phtml';

    /**
     * @var string
     */
    protected $multipleTemplate = 'Magento_Catalog::product/composite/fieldset/options/view/multiple.phtml';

    /**
     * @var CheckableFactory
     */
    private $checkableFactory;

    /**
     * Select constructor.
     * @param CheckableFactory|null $checkableFactory
     */
    public function __construct(
        CheckableFactory $checkableFactory
    ) {
        $this->checkableFactory = $checkableFactory;
    }

    /**
     * Return html for control element
     *
     * @return string
     */
    public function getOptionHtml($option, $product): string
    {
        $optionType = $option->getType();

        $optionBlock = $this->checkableFactory->create();

        if ($optionType === Option::OPTION_TYPE_DROP_DOWN ||
            $optionType === Option::OPTION_TYPE_MULTIPLE
        ) {
            $optionBlock = $optionBlock->setTemplate($this->multipleTemplate);
        }

        if ($optionType === Option::OPTION_TYPE_RADIO ||
            $optionType === Option::OPTION_TYPE_CHECKBOX
        ) {
            $optionBlock = $optionBlock->setTemplate($this->checkableTemplate);
        }

        return $optionBlock
            ->setOption($option)
            ->setProduct($product)
            ->toHtml();
    }
}
