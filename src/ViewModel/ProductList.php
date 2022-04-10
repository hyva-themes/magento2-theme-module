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
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection as ProductLinkCollection;
use Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory as ProductLinkCollectionFactory;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Review\Model\ResourceModel\Review\Summary as ReviewSummaryResource;
use Magento\Review\Model\Review;
use function array_filter as filter;
use function array_map as map;
use function array_merge as merge;
use function array_slice as slice;
use function array_unique as unique;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ProductList implements ArgumentInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ?int
     */
    private $categoryIdFilter;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

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

    /**
     * @var int
     */
    private $maxCrosssellItemCount;

    /**
     * @var ReviewSummaryResource
     */
    private $reviewSummaryResource;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var bool
     */
    private $isIncludingReviewSummary;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductCollectionFactory $productCollectionFactory,
        ProductLinkCollectionFactory $productLinkCollectionFactory,
        ProductLinkFactory $productLinkFactory,
        CatalogConfig $catalogConfig,
        CollectionProcessorInterface $collectionProcessor,
        CategoryFactory $categoryFactory,
        ReviewSummaryResource $reviewSummaryResource,
        bool $isIncludingReviewSummary = true,
        int $maxCrosssellItemCount = 4
    ) {
        $this->searchCriteriaBuilder        = $searchCriteriaBuilder;
        $this->filterBuilder                = $filterBuilder;
        $this->sortOrderBuilder             = $sortOrderBuilder;
        $this->productCollectionFactory     = $productCollectionFactory;
        $this->productLinkFactory           = $productLinkFactory;
        $this->catalogConfig                = $catalogConfig;
        $this->productLinkCollectionFactory = $productLinkCollectionFactory;
        $this->collectionProcessor          = $collectionProcessor;
        $this->reviewSummaryResource        = $reviewSummaryResource;
        $this->isIncludingReviewSummary     = $isIncludingReviewSummary;
        $this->maxCrosssellItemCount        = $maxCrosssellItemCount;
        $this->categoryFactory              = $categoryFactory;
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        $collection = $this->createProductCollection();
        $this->applyCriteria($this->searchCriteriaBuilder->create(), $collection);
        $collection->each('setDoNotUseCategoryId', [true]);

        return $collection->getItems();
    }

    /**
     * @param QuoteItem ...$cartItems
     * @return ProductInterface[]
     */
    public function getCrosssellItems(QuoteItem ...$cartItems): array
    {
        if (empty($cartItems)) {
            return [];
        }

        $criteria = $this->searchCriteriaBuilder->create();
        $criteria->setPageSize($this->maxCrosssellItemCount);

        // return most recently added product crosssell items first
        usort($cartItems, function (QuoteItem $itemA, QuoteItem $itemB) {
            return ($itemA->getCreatedAt() <=> $itemB->getCreatedAt()) * -1;
        });

        $exclude = map(function (QuoteItem $item): Product {
            return $item->getProduct();
        }, $cartItems);

        $items = $this->loadCrosssellItems($criteria, slice($cartItems, 0, 1), $exclude);

        // if more crosssell items are expected, load crosssells for other products in cart
        if (count($items) < $this->maxCrosssellItemCount && count($cartItems) > 1 &&
            $criteria->getPageSize() >= $this->maxCrosssellItemCount) {
            $criteria->setPageSize($this->maxCrosssellItemCount - count($items));
            $items = merge($items, $this->loadCrosssellItems($criteria, slice($cartItems, 1), merge($exclude, $items)));
        }

        return slice($items, 0, $this->maxCrosssellItemCount);
    }

    /**
     * @param SearchCriteriaInterface $criteria
     * @param QuoteItem[] $linkSources
     * @param Product[] $exclude
     * @return ProductInterface[]
     */
    private function loadCrosssellItems(SearchCriteriaInterface $criteria, array $linkSources, array $exclude): array
    {
        $linkSourceIds = unique(filter(map([$this, 'extractProductId'], $linkSources)));
        $excludeIds    = unique(filter(map([$this, 'extractProductId'], $exclude)));
        $collection    = $this->createProductLinkCollection('crosssell', $linkSourceIds);
        $collection->addExcludeProductFilter($excludeIds);
        $this->applyCriteria($criteria, $collection);
        $collection->setGroupBy(); // group by product id field - required to avoid duplicate products in collection
        $collection->each('setDoNotUseCategoryId', [true]);

        return $collection->getItems();
    }

    private function applyCriteria(SearchCriteriaInterface $criteria, AbstractDb $collection): void
    {
        $this->collectionProcessor->process($criteria, $collection);
        if ($this->categoryIdFilter && $collection instanceof ProductCollection) {
            $category = $this->categoryFactory->create();
            $category->setData($category->getIdFieldName(), $this->categoryIdFilter);
            $collection->addCategoryFilter($category);
        }
        $this->categoryIdFilter = null;
    }

    private function extractProductId($item)
    {
        return $item->getProductId()
            ?? $item->getEntityId()
            ?? $item->getId();
    }

    private function createProductLinkCollection(string $linkType, array $productIds): ProductLinkCollection
    {
        $collection = $this->productLinkCollectionFactory->create(['productIds' => $productIds]);
        $collection->setLinkModel($this->getLinkTypeModel($linkType))
                   ->setIsStrongMode()
                   ->setPositionOrder()
                   ->addStoreFilter()
                   ->addAttributeToSelect($this->catalogConfig->getProductAttributes());

        $this->loadReviewSummariesIfEnabled($collection);

        return $collection;
    }

    private function createProductCollection(): ProductCollection
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addStoreFilter()
                   ->addAttributeToSelect($this->catalogConfig->getProductAttributes());

        $this->loadReviewSummariesIfEnabled($collection);

        return $collection;
    }

    private function loadReviewSummariesIfEnabled(ProductCollection $collection): void
    {
        if ($this->isIncludingReviewSummary) {
            $this->reviewSummaryResource->appendSummaryFieldsToCollection(
                $collection,
                $collection->getStoreId(),
                Review::ENTITY_PRODUCT_CODE
            );
        }
    }

    /**
     * @param Product|ProductInterface ...$products
     * @return ProductInterface[]
     */
    public function getRelatedItems(Product ...$products): array
    {
        return call_user_func_array([$this, 'getLinkedItems'], merge(['related'], $products));
    }

    /**
     * @param Product|ProductInterface ...$products
     * @return ProductInterface[]
     */
    public function getUpsellItems(Product ...$products): array
    {
        return call_user_func_array([$this, 'getLinkedItems'], merge(['upsell'], $products));
    }

    /**
     * @param string $linkType
     * @param Product|ProductInterface|QuoteItem ...$items
     * @return ProductInterface[]
     */
    public function getLinkedItems(string $linkType, ...$items): array
    {
        return $linkType === 'crosssell'
            ? call_user_func_array([$this, 'getCrosssellItems'], $items)
            : call_user_func_array([$this, 'loadLinkedItems'], merge([$linkType], $items));
    }

    private function loadLinkedItems(string $linkType, ...$items): array
    {
        // $items can be anything with a getProductId() or getEntityId() or getId() method
        $productIds = filter(map([$this, 'extractProductId'], $items));
        $collection = $this->createProductLinkCollection($linkType, $productIds);
        $collection->addExcludeProductFilter($productIds);

        $this->applyCriteria($this->searchCriteriaBuilder->create(), $collection);

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
        // Handle single categories as a special case to allow sorting by category position
        if ($field === 'category_id' && preg_match('/^\d+$/', $value)) {
            $this->categoryIdFilter = $value;
        } else {
            $this->searchCriteriaBuilder->addFilter($field, $value, $conditionType);
        }

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

    public function includeReviewSummary(): self
    {
        $this->isIncludingReviewSummary = true;

        return $this;
    }

    public function excludeReviewSummary(): self
    {
        $this->isIncludingReviewSummary = false;

        return $this;
    }
}
