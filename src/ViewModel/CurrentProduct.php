<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use ArrayIterator;
use Iterator;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductInterfaceFactory;
use Traversable;
use function array_merge as merge;
use function array_unique as unique;
use function array_values as values;

class CurrentProduct implements ArgumentInterface, IdentityInterface
{
    /**
     * @var ProductInterface
     */
    protected $currentProduct;

    /**
     * @var ProductInterfaceFactory
     */
    protected $productFactory;

    /**
     * @var string[]
     */
    private $cacheIdentities = [];

    public function __construct(ProductInterfaceFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }

    public function set(ProductInterface $product): void
    {
        $this->currentProduct = $product;
        $this->collectIdentities($product);
    }

    /**
     * @return ProductInterface
     * @throws \RuntimeException
     */
    public function get(): ProductInterface
    {
        if ($this->exists()) {
            return $this->currentProduct;
        }
        throw new \RuntimeException('Product is not set on ProductRegistry.');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return ($this->currentProduct && $this->currentProduct->getId());
    }

    /**
     * Returns an iterator to loop over products while setting CurrentProduct to the current element in each iteration.
     * Afterwards, the original value is restored.
     *
     * Example:
     *
     *      foreach($currentProduct->loop($productCollection)):
     *          echo $block->getChildHtml('item');
     *      endforeach;
     *
     * Now the child block "item" receives each item of $productCollection via the CurrentProduct view model
     *
     * @param iterable $iterable A product collection or array of products
     * @return Iterator<ProductInterface>
     */
    public function loop(iterable $iterable): Iterator
    {
        if (is_array($iterable)) {
            $iterable = new ArrayIterator($iterable);
        }
        return new class ($iterable, $this) extends \IteratorIterator {
            /** @var CurrentProduct */
            private $currentProduct;
            /** @var ProductInterface */
            private $originalCurrentProduct;

            public function __construct(Traversable $iterator, CurrentProduct $currentProduct)
            {
                parent::__construct($iterator);
                $this->currentProduct = $currentProduct;
            }

            #[\ReturnTypeWillChange]
            public function rewind()
            {
                $this->originalCurrentProduct = $this->currentProduct->get();
                parent::rewind();
            }

            #[\ReturnTypeWillChange]
            public function current()
            {
                $current = parent::current();
                $this->currentProduct->set($current);
                return $current;
            }

            #[\ReturnTypeWillChange]
            public function valid()
            {
                $valid = parent::valid();
                if (!$valid && $this->originalCurrentProduct) {
                    $this->currentProduct->set($this->originalCurrentProduct);
                }
                return $valid;
            }
        };
    }

    private function collectIdentities(ProductInterface $product)
    {
        if ($product instanceof IdentityInterface) {
            $this->cacheIdentities = merge($this->cacheIdentities, values($product->getIdentities()));
        }
    }

    public function getIdentities()
    {
        return unique($this->cacheIdentities);
    }
}
