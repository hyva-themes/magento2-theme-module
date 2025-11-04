<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Config as CatalogConfig;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\LinkFactory as ProductLinkFactory;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
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
use Magento\Framework\EntityManager\MetadataPool as EntityMetadataPool;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface as ConfigScope;
use Magento\Framework\Exception\NoSuchEntityException;
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
    public const XML_CROSSSELL_PRODUCTS_LIMIT = 'hyva_theme_catalog/crosssell_products/limit';

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var ?int
     */
    private $categoryIdFilter;

    /**
     * @var bool
     */
    private $shouldLoadPriceData = false;

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

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var bool
     */
    private $useAnchorAttribute;

    /**
     * @var ProductVisibility
     */
    private $productVisibility;

    /**
     * @var EntityMetadataPool
     */
    private $entityMetadataPool;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string|null
     */
    private $memoizedProductLinkField;

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
        CategoryRepositoryInterface $categoryRepository,
        ReviewSummaryResource $reviewSummaryResource,
        ProductVisibility $productVisibility,
        EntityMetadataPool $entityMetadataPool,
        ScopeConfigInterface $scopeConfig,
        bool $isIncludingReviewSummary = true,
        int $maxCrosssellItemCount = 4,
        bool $useAnchorAttribute = false
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
        $this->productVisibility            = $productVisibility;
        $this->entityMetadataPool           = $entityMetadataPool;
        $this->scopeConfig                  = $scopeConfig;
        $this->isIncludingReviewSummary     = $isIncludingReviewSummary;
        $this->maxCrosssellItemCount        = $maxCrosssellItemCount;
        $this->categoryFactory              = $categoryFactory;
        $this->categoryRepository           = $categoryRepository;
        $this->useAnchorAttribute           = $useAnchorAttribute;
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

        $maxCrosssellItemCount = $this->getConfiguredMaxCrosssellItemCount() ?: $this->maxCrosssellItemCount;

        $criteria = $this->searchCriteriaBuilder->create();
        $criteria->setPageSize($maxCrosssellItemCount);

        // return most recently added product crosssell items first
        usort($cartItems, function (QuoteItem $itemA, QuoteItem $itemB) {
            return ($itemA->getCreatedAt() <=> $itemB->getCreatedAt()) * -1;
        });

        $exclude = map(function (QuoteItem $item): Product {
            return $item->getProduct();
        }, $cartItems);

        $items = $this->loadCrosssellItems($criteria, slice($cartItems, 0, 1), $exclude);

        // if more crosssell items are expected, load crosssells for other products in cart
        if (count($items) < $maxCrosssellItemCount && count($cartItems) > 1 &&
            $criteria->getPageSize() >= $maxCrosssellItemCount) {
            $criteria->setPageSize($maxCrosssellItemCount - count($items));
            $items = merge($items, $this->loadCrosssellItems($criteria, slice($cartItems, 1), merge($exclude, $items)));
        }

        return slice($items, 0, $maxCrosssellItemCount);
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
        if ($collection instanceof ProductCollection) {
            if ($this->categoryIdFilter) {
                $category = $this->getCategory();
                $collection->addCategoryFilter($category);
            }
            if ($this->shouldLoadPriceData) {
                $collection->addPriceData();
            }
        }

        $this->categoryIdFilter = null;
        $this->shouldLoadPriceData = false;
    }

    private function extractProductId($item)
    {
        $linkField = $this->getProductLinkField();
        $product = $item->getProduct();
        return ($product ? $product->getData($linkField) : null)
            ?? $item->getProductId()
            ?? $item->getData($linkField)
            ?? $item->getId();
    }

    /**
     * Return either entity_id (on open source) or row_id (on commerce, for staging)
     */
    private function getProductLinkField(): string
    {
        if (! $this->memoizedProductLinkField) {
            $this->memoizedProductLinkField = $this->entityMetadataPool->getMetadata(ProductInterface::class)->getLinkField();
        }

        return $this->memoizedProductLinkField;
    }

    private function createProductLinkCollection(string $linkType, array $productIds): ProductLinkCollection
    {
        $collection = $this->productLinkCollectionFactory->create(['productIds' => $productIds]);
        $collection->setLinkModel($this->getLinkTypeModel($linkType))
                   ->setIsStrongMode()
                   ->setPositionOrder()
                   ->addStoreFilter()
                   ->setVisibility($this->productVisibility->getVisibleInCatalogIds())
                   ->addAttributeToSelect($this->catalogConfig->getProductAttributes());

        $this->loadReviewSummariesIfEnabled($collection);

        return $collection;
    }

    private function createProductCollection(): ProductCollection
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addStoreFilter()
                   ->setVisibility($this->productVisibility->getVisibleInCatalogIds())
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
        if ($field === 'category_id' && ! is_array($value) && preg_match('/^\d+$/', (string) $value)) {
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

    public function getConfiguredMaxCrosssellItemCount(): ?int
    {
        $value = $this->scopeConfig->getValue(self::XML_CROSSSELL_PRODUCTS_LIMIT, ConfigScope::SCOPE_STORE);

        return $value !== null && $value !== '' ? (int)$value : null;
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

    /**
     * This method only has an effect if a single category ID filter is set, and that category is an anchor category
     */
    public function includeChildCategoryProducts(): self
    {
        $this->useAnchorAttribute = true;

        return $this;
    }

    public function excludeChildCategoryProducts(): self
    {
        $this->useAnchorAttribute = false;

        return $this;
    }

    public function addPriceData(): self
    {
        $this->shouldLoadPriceData = true;

        return $this;
    }

    private function getCategory(): Category
    {
        if ($this->useAnchorAttribute) {
            // phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedCatch
            try {
                // Return a loaded category, which will cause child category products to be displayed for anchor categories
                $category = $this->categoryRepository->get($this->categoryIdFilter);
                if ($category instanceof Category) {
                    return $category;
                }
            } catch (NoSuchEntityException $e) {
                // If the category does not exist, there will be no child category products anyway, so we can ignore this error
            }
        }

        // Return unloaded category, which will cause only the products directly associated with the category to be displayed
        $category = $this->categoryFactory->create();
        $category->setData($category->getIdFieldName(), $this->categoryIdFilter);

        return $category;
    }
}
