<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ProductStockItem implements ArgumentInterface
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    public function __construct(StockRegistryInterface $stockRegistry)
    {
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * This method is private so it can be refactored to use a non-deprecated API in future if needed.
     *
     * @param ProductInterface|Product $product
     * @return StockItemInterface|null
     */
    private function getStockItem(ProductInterface $product): ?StockItemInterface
    {
        if (! $product->hasData('stock_item')) {
            $stockItem = $this->loadStockItem((int) $product->getId());
            $product->setData('stock_item', $stockItem);
        }
        return $product->getData('stock_item');
    }

    private function loadStockItem(int $productId): ?StockItemInterface
    {
        try {
            return $productId
                ? $this->stockRegistry->getStockItem($productId)
                : null;
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @param ProductInterface|Product $product
     */
    public function getUseConfigManageStock(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getUseConfigManageStock();
    }

    /**
     * @param ProductInterface|Product $product
     * @return float|int
     */
    public function getQty(ProductInterface $product)
    {
        if (! ($stockItem = $this->getStockItem($product))) {
            return 0;
        }
        return $stockItem->getIsQtyDecimal()
            ? (float) $stockItem->getQty()
            : (int) $stockItem->getQty();
    }

    /**
     * @param ProductInterface|Product $product
     * @return bool
     */
    public function isQtyDecimal(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getIsQtyDecimal();
    }

    /**
     * @param ProductInterface|Product $product
     * @return bool
     */
    public function isInStock(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getIsInStock();
    }

    /**
     * Return minimum qty allowed in cart or NULL when there is no limitation
     *
     * @param ProductInterface $product
     * @return float|int|null
     */
    public function getMinSaleQty(ProductInterface $product)
    {
        if (! ($stockItem = $this->getStockItem($product))) {
            return 0;
        }
        $min = $stockItem->getMinSaleQty();
        return isset($min)
            ? ($stockItem->getIsQtyDecimal() ? (float) $min : (int) $min)
            : null;
    }

    /**
     * Return maximum qty allowed in cart or NULL when there is no limitation
     *
     * @param ProductInterface|Product $product
     * @return float|int|null
     */
    public function getMaxSaleQty(ProductInterface $product)
    {
        if (! ($stockItem = $this->getStockItem($product))) {
            return 0;
        }
        $max = $stockItem->getMaxSaleQty();
        return isset($max)
            ? ($stockItem->getIsQtyDecimal() ? (float) $max : (int) $max)
            : null;
    }

    /**
     * @param ProductInterface|Product $product
     * @return bool
     */
    public function areQtyIncrementsEnabled(ProductInterface $product): bool
    {
        return (($stockItem = $this->getStockItem($product))) && $stockItem->getEnableQtyIncrements();
    }

    /**
     * @param ProductInterface|Product $product
     * @return false|float
     */
    public function getQtyIncrements(ProductInterface $product)
    {
        if (! ($stockItem = $this->getStockItem($product))) {
            return false;
        }
        return $stockItem->getQtyIncrements();
    }

    /**
     * @param ProductInterface $product
     * @return int|null
     */
    public function getBackorders(ProductInterface $product)
    {
        return ($stockItem = $this->getStockItem($product))
            ? $stockItem->getBackorders()
            : null;
    }
}
