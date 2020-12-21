<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;

class ProductRegistry implements ArgumentInterface
{
    /**
     * @var ProductInterface
     */
    private $currentProduct;

    /**
     * @var ProductInterfaceFactory
     */
    private ProductInterfaceFactory $productFactory;

    public function __construct(ProductInterfaceFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function set(ProductInterface $product): void
    {
        $this->currentProduct = $product;
    }

    public function get(): ProductInterface
    {
        return $this->currentProduct ?? $this->createNullProduct();
    }

    private function createNullProduct(): ProductInterface
    {
        return $this->productFactory->create();
    }
}
