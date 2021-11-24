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
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\Data\ListCriteria;
use Magento\Catalog\Model\ProductLink\Data\ListResultInterface;
use Magento\Catalog\Model\ProductLink\ProductLinkQuery;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item as CartItem;
use function array_map as map;
use function array_merge as merge;
use function array_unique as unique;

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
     * @var ProductLinkQuery
     */
    private $productLinkQuery;

    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        ProductRepositoryInterface $productRepository,
        ProductLinkQuery $productLinkQuery
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder         = $filterBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->productRepository     = $productRepository;
        $this->productLinkQuery      = $productLinkQuery;
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        return $this->productRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }

    /**
     * @param CartItem|CartItemInterface ...$cartItems
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
     * @param Product|ProductInterface|CartItem|CartItemInterface ...$items
     * @return ProductInterface[]
     */
    public function getLinkedItems(string $linkType, ...$items): array
    {
        // $items can be anything with a getSku() method
        return $this->addFilter('sku', $this->getLinkedSkus($linkType, ...$items), 'in')->getItems();
    }

    /**
     * @param string $linkType
     * @param Product|ProductInterface|CartItem|CartItemInterface ...$items
     * @return ProductInterface[]
     */
    private function getLinkedSkus(string $linkType, ...$items): array
    {
        if (empty($items)) {
            return [];
        }

        // $items can be anything with a getSku() method
        $criteriaList = map(function ($item) use ($linkType): ListCriteria {
            return new ListCriteria($item->getSku(), [$linkType], $item instanceof Product ? $item : null);
        }, $items);

        $links = merge([], ...map(function (ListResultInterface $listResult): array {
            return (array) $listResult->getResult();
        }, $this->productLinkQuery->search($criteriaList)));

        return unique(map(function (ProductLinkInterface $productLink): string {
            return $productLink->getLinkedProductSku();
        }, $links));
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
