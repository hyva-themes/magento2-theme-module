<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Catalog\Pricing\Price\SpecialPriceBulkResolverInterface;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\ObjectManagerInterface;

/**
 * Magento 2.4.8 introduced Magento\Catalog\Pricing\Price\SpecialPriceBulkResolverInterface and
 * the implementation \Magento\Catalog\Pricing\Price\SpecialPriceBulkResolver.
 * This class is a wrapper aiming to be backward compatible with Magento versions < 2.4.8.
 */
class SpecialPriceBulkResolver
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function generateSpecialPriceMap(int $websiteId, ?AbstractCollection $productCollection): array
    {
        // In Magento 2.4.8 and newer the interface exists
        if (interface_exists(SpecialPriceBulkResolverInterface::class)) {
            // The core method signature names the first param $storeId, but it in fact has to be a website ID to work correctly
            return $this->objectManager->get(SpecialPriceBulkResolverInterface::class)->generateSpecialPriceMap($websiteId, $productCollection);
        }
        return [];
    }
}
