<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Model\Product\Exception as ProductException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;

class CurrentProduct implements ArgumentInterface
{
    /**
     * @var ProductInterface
     */
    private $currentProduct;

    /**
     * @var ProductInterfaceFactory
     */
    private $productFactory;

    public function __construct(ProductInterfaceFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function set(ProductInterface $product): void
    {
        $this->currentProduct = $product;
    }

    /**
     * @return ProductInterface
     * @throws ProductException
     */
    public function get(): ProductInterface
    {
        if ($this->exists()) {
            return $this->currentProduct;
        }
        throw new ProductException(__('Product is not set on ProductRegistry.'));
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return ($this->currentProduct && $this->currentProduct->getId());
    }
}
