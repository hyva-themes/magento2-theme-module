<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Hyva\Theme\ViewModel\Product\Registry as ProductRegistry;

/**
 * Class RegisterCurrentProduct
 *
 * We use an observer to write the current product to a custom
 * Registry ViewModel, which is a shared instance to be used from
 * any template file.
 *
 * This concept is derived from:
 * https://github.com/Vinai/module-current-product-example
 */
class RegisterCurrentProduct implements ObserverInterface
{
    /**
     * @var ProductRegistry
     */
    private $productRegistry;

    public function __construct(ProductRegistry $currentProduct)
    {
        $this->productRegistry = $currentProduct;
    }

    public function execute(Event $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData('product');
        $this->productRegistry->set($product);
    }
}
