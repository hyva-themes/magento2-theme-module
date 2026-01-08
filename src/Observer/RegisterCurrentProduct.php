<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\ViewModel\CurrentCategory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Hyva\Theme\ViewModel\CurrentProduct;

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
     * @var CurrentProduct
     */
    protected $currentProduct;

    /**
     * @var CurrentCategory
     */
    protected $currentCategory;

    public function __construct(
        CurrentProduct $currentProduct,
        CurrentCategory $currentCategory
    ) {
        $this->currentProduct = $currentProduct;
        $this->currentCategory = $currentCategory;
    }

    public function execute(Event $event)
    {
        /** @var ProductInterface $product */
        $product = $event->getData('product');
        $this->currentProduct->set($product);

        if ($product->getCategory()) {
            $this->currentCategory->set($product->getCategory());
        }
    }
}
