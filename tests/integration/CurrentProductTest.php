<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\CurrentProduct;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Catalog\ProductBuilder;
use TddWizard\Fixtures\Catalog\ProductFixturePool;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CurrentProductTest extends TestCase
{
    /**
     * @var CurrentProduct
     */
    private $currentProduct;

    /**
     * @var ProductFixturePool
     */
    private $products;

    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->currentProduct = $objectManager->get(CurrentProduct::class);
        $this->productCollectionFactory = $objectManager->get(CollectionFactory::class);
        $this->productRepository = $objectManager->get(ProductRepositoryInterface::class);
        $this->products = new ProductFixturePool();
        $this->products->add(ProductBuilder::aSimpleProduct()->withSku('original')->build());
        $this->products->add(ProductBuilder::aSimpleProduct()->build());
        $this->products->add(ProductBuilder::aSimpleProduct()->build());
        $this->products->add(ProductBuilder::aSimpleProduct()->build());
    }

    protected function tearDown(): void
    {
        $this->products->rollback();
    }

    /**
     * @test
     */
    public function reset_after_loop()
    {
        $originalProduct = $this->productRepository->get('original');
        $this->currentProduct->set($originalProduct);
        foreach ($this->currentProduct->loop($this->productCollectionFactory->create()) as $product) {
            $this->assertEquals(
                $product->getSku(),
                $this->currentProduct->get()->getSku(),
                'Current Product should be changed inside loop'
            );
        }
        $this->assertEquals(
            $originalProduct->getSku(),
            $this->currentProduct->get()->getSku(),
            'Current product should be unchanged after loop'
        );
    }

    /**
     * @test
     */
    public function aggregates_cache_identities_for_loop()
    {
        $originalProduct = $this->productRepository->get('original');
        $this->currentProduct->set($originalProduct);
        $tags[] = $originalProduct->getIdentities();
        foreach ($this->currentProduct->loop($this->productCollectionFactory->create()) as $product) {
            $tags[] = $product->getIdentities();
        }
        $expected = array_unique(array_merge(...$tags));
        $actual = $this->currentProduct->getIdentities();
        sort($expected);
        sort($actual);
        $this->assertGreaterThan(
            count($originalProduct->getIdentities()),
            count($actual),
            'Expected to aggregate more cache tags than only the ones provided by the original product'
        );
        $this->assertSame($expected, $actual, 'Cache tags do not match');
    }
}
