<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\BaseOldPricePolyfill;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Variations\Prices as ConfigurablePriceVariations;
use Magento\Framework\Locale\Format as LocaleFormat;
use Magento\Framework\Pricing\PriceInfo\Base as BasePriceInfo;

class AddBaseOldPriceToConfigurableProductPriceVariations
{
    /**
     * @var LocaleFormat
     */
    private $localeFormat;

    public function __construct(LocaleFormat $localeFormat)
    {
        $this->localeFormat = $localeFormat;
    }

    /**
     * Add price excluding tax as baseOldPrice if not set (for Magento versions < 2.4.2)
     *
     * See https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/153
     * See https://github.com/magento/magento2/pull/27832/files
     *
     * @param ConfigurablePriceVariations $subject
     * @param array $result
     * @param BasePriceInfo $priceInfo
     * @return array
     */
    public function afterGetFormattedPrices(
        ConfigurablePriceVariations $subject,
        array $result,
        BasePriceInfo $priceInfo
    ) {
        if (!isset($result['baseOldPrice'])) {
            $baseOldPrice           = $priceInfo->getPrice('regular_price')->getAmount()->getBaseAmount();
            $result['baseOldPrice'] = ['amount' => $this->localeFormat->getNumber($baseOldPrice)];
        }

        return $result;
    }
}
