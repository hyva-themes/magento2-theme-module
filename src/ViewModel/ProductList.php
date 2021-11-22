<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory as ProductLinkCollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote\Item as CartItem;

use function array_filter as filter;
use function array_map as map;

class ProductList implements ArgumentInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var ProductLinkFactory
     */
    private $productLinkFactory;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * @var ProductLinkCollectionFactory
     */
    private $productLinkCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductRepositoryInterface $productRepository,
        ProductLinkCollectionFactory $productLinkCollectionFactory,
        ProductLinkFactory $productLinkFactory,
        CatalogConfig $catalogConfig,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->filterBuilder                = $filterBuilder;
        $this->sortOrderBuilder             = $sortOrderBuilder;
        $this->productRepository            = $productRepository;
        $this->productLinkFactory           = $productLinkFactory;
        $this->catalogConfig                = $catalogConfig;
        $this->productLinkCollectionFactory = $productLinkCollectionFactory;
        $this->collectionProcessor          = $collectionProcessor;
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        return $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * @param CartItem ...$cartItems
     * @return ProductInterface[]
     */
    public function getCrosssellItems(CartItem ...$cartItems): array
    {
        return $this->getLinkedItems('crosssell', ...$cartItems);
    }

    /**
     * @param Product|ProductInterface ...$products
     * @return ProductInterface[]
     */
    public function getRelatedItems(Product ...$products): array
    {
        return $this->getLinkedItems('related', ...$products);
    }

    /**
     * @param Product|ProductInterface ...$products
     * @return ProductInterface[]
     */
    public function getUpsellItems(Product ...$products): array
    {
        return $this->getLinkedItems('upsell', ...$products);
    }

    /**
     * @param string $linkType
     * @param Product|ProductInterface|CartItem ...$items
     * @return ProductInterface[]
     */
    public function getLinkedItems(string $linkType, ...$items): array
    {
        // $items can be anything with a getProductId() or getEntityId() or getId() method
        $productIds = filter(map(function ($item) {
            return $item->getProductId()
                ?? $item->getEntityId()
                ?? $item->getId();
        }, $items));
        $collection = $this->productLinkCollectionFactory->create(['productIds' => $productIds]);
        $collection->setLinkModel($this->getLinkTypeModel($linkType))
                   ->setIsStrongMode()
                   ->setPositionOrder()
                   ->addStoreFilter()
                   ->addAttributeToSelect($this->catalogConfig->getProductAttributes());

        $this->collectionProcessor->process($this->searchCriteriaBuilder->create(), $collection);

        $collection->setGroupBy(); // group by product id field - required to avoid duplicate products in collection

        $collection->each('setDoNotUseCategoryId', [true]);

        return $collection->getItems();
    }

    private function getLinkTypeModel(string $linkType): Product\Link
    {
        $linkModel = $this->productLinkFactory->create();
        switch ($linkType) {
            case 'crosssell':
                $linkModel->useCrossSellLinks();
                break;
            case 'related':
                $linkModel->useRelatedLinks();
                break;
            case 'upsell':
                $linkModel->useUpSellLinks();
                break;
        }
        return $linkModel;
    }

    /**
     * Add filter to be applied when an item getter is called.
     *
     * Filters added by consecutive calls to addFilter() are combined with AND.
     *
     * @param string $field
     * @param mixed $value
     * @param string $conditionType
     * @return $this
     */
    public function addFilter($field, $value, $conditionType = 'eq'): self
    {
        $this->searchCriteriaBuilder->addFilter($field, $value, $conditionType);

        return $this;
    }

    /**
     * Add multiple filters combined with OR.
     *
     * Each filter array has the structure [field, value, conditionType].
     * Filters within a group will be combined with OR.
     * Multiple filter groups will be added with AND.
     *
     * Example:
     *
     * $productList->addFilterGroup(
     *    ['sku', ['abc', 'def'], 'in'],
     *    ['color', 'red, 'eq']
     * );
     *
     * The above example corresponds to the SQL condition
     *
     *   WHERE (sku IN ('abc', 'def')) OR (color = 'red')
     *
     * @param array ...$filters
     * @return $this
     */
    public function addFilterGroup(array ...$filters): self
    {
        $filterInstances = map(function (array $filter): Filter {
            [$field, $value, $conditionType] = $filter;
            $this->filterBuilder->setField($field);
            $this->filterBuilder->setConditionType($conditionType);
            $this->filterBuilder->setValue($value);

            return $this->filterBuilder->create();
        }, $filters);
        $this->searchCriteriaBuilder->addFilters($filterInstances);

        return $this;
    }

    public function addAscendingSortOrder(string $field): self
    {
        $this->sortOrderBuilder->setField($field);
        $this->sortOrderBuilder->setAscendingDirection();
        $this->searchCriteriaBuilder->addSortOrder($this->sortOrderBuilder->create());

        return $this;
    }

    public function addDescendingSortOrder(string $field): self
    {
        $this->sortOrderBuilder->setField($field);
        $this->sortOrderBuilder->setDescendingDirection();
        $this->searchCriteriaBuilder->addSortOrder($this->sortOrderBuilder->create());

        return $this;
    }

    public function setPageSize($pageSize): self
    {
        $this->searchCriteriaBuilder->setPageSize($pageSize);

        return $this;
    }

    public function setCurrentPage($currentPage): self
    {
        $this->searchCriteriaBuilder->setCurrentPage($currentPage);

        return $this;
    }
}
