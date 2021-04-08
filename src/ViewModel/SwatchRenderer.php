<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Swatches\Helper\Data as SwatchHelper;

class SwatchRenderer implements ArgumentInterface
{
    /**
     * @var SwatchHelper
     */
    private $swatchHelper;

    /**
     * @param SwatchHelper $swatchHelper
     */
    public function __construct(SwatchHelper $swatchHelper)
    {
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @param Attribute $attribute
     */
    public function isSwatchAttribute($attribute): bool
    {
        return $this->swatchHelper->isSwatchAttribute($attribute);
    }
}
