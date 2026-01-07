<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\BaseOldPricePolyfill;

use Magento\Catalog\Block\Product\View as ProductViewBlock;
use Magento\Catalog\Model\Product;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;

class AddBaseOldPriceToProductViewBlockJson
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
     * @param ProductViewBlock $subject
     * @param string $resultJson
     * @return string
     */
    public function afterGetJsonConfig(ProductViewBlock $subject, string $resultJson): string
    {
        $priceInfo                 = $this->jsonSerializer->unserialize($resultJson);
        $priceInfo['baseOldPrice'] = $priceInfo['baseOldPrice'] ?? $this->getBaseOldPrice($subject->getProduct());

        return $this->jsonSerializer->serialize($priceInfo);
    }

    private function getBaseOldPrice(Product $product): array
    {
        return [
            'amount'      => $product->getPriceInfo()->getPrice('regular_price')->getAmount()->getBaseAmount() * 1,
            'adjustments' => [],
        ];
    }
}
