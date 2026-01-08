<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\BaseOldPricePolyfill;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ConfigurableProductTypeBlock;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class AddBaseOldPriceToConfigurableProductOptions
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    public function __construct(JsonSerializer $jsonSerializer)
    {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Add price excluding tax as baseOldPrice if not set (for Magento versions < 2.4.2)
     *
     * See https://gitlab.hyva.io/hyva-themes/magento2-default-theme/-/issues/153
     * See https://github.com/magento/magento2/pull/27832/files
     *
     * @param ConfigurableProductTypeBlock $subject
     * @param string $jsonResult
     * @return string
     */
    public function afterGetJsonConfig(ConfigurableProductTypeBlock $subject, $jsonResult)
    {
        $priceInfo = $this->jsonSerializer->unserialize($jsonResult);

        $priceInfo['optionPrices'] = $this->mergeMissingBaseOldPrice(
            $subject->getAllowProducts(),
            $priceInfo['optionPrices'] ?? []
        );

        return $this->jsonSerializer->serialize($priceInfo);
    }

    private function mergeMissingBaseOldPrice(array $allowedProducts, array $optionPrices): array
    {
        foreach ($allowedProducts as $product) {
            if (isset($optionPrices[$product->getId()]) && !isset($optionPrices[$product->getId()]['baseOldPrice'])) {
                $optionPrices[$product->getId()]['baseOldPrice'] = $this->getBaseOldPrice($product);
            }
        }
        return $optionPrices;
    }

    private function getBaseOldPrice(Product $product): array
    {
        return [
            'amount' => $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount() * 1,
        ];
    }
}
