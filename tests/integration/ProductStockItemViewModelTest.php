<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\ProductStockItem;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class ProductStockItemViewModelTest extends TestCase
{
    public function testImplementsArgumentInterface(): void
    {
        $sut = ObjectManager::getInstance()->create(ProductStockItem::class);
        $this->assertInstanceOf(ArgumentInterface::class, $sut);
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoConfigFixture current_store cataloginventory/item_options/max_sale_qty 100
     */
    public function testReturnsAProductStockItem(): void
    {
        /** @var ProductRepositoryInterface $productRepo */
        $productRepo = ObjectManager::getInstance()->get(ProductRepositoryInterface::class);
        $product = $productRepo->get(/* product sku fixture */ 'simple');

        /** @var ProductStockItem $sut */
        $sut = ObjectManager::getInstance()->create(ProductStockItem::class);

        $this->assertTrue($sut->getUseConfigManageStock($product));
        $this->assertSame(100, $sut->getQty($product));
        $this->assertFalse($sut->isQtyDecimal($product));
        $this->assertTrue($sut->isInStock($product));
        $this->assertSame(1, $sut->getMinSaleQty($product));
        $this->assertSame(100, $sut->getMaxSaleQty($product));
    }
}
