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
use Magento\Catalog\Model\ProductLink\ProductLinkQuery;
use Magento\Catalog\Model\ProductLink\Repository as ProductLinkRepository;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;

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
     * @var ProductLinkRepository
     */
    private $productLinkRepository;

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
     * @param Product|ProductInterface $product
     * @return ProductInterface[]
     */
    public function getCrosssellItems(Product $product): array
    {
        return $this->getLinkedItems($product, 'crosssell');
    }

    /**
     * @param Product|ProductInterface $product
     * @return ProductInterface[]
     */
    public function getRelatedItems(Product $product): array
    {
        return $this->getLinkedItems($product, 'related');
    }

    /**
     * @param Product|ProductInterface $product
     * @return ProductInterface[]
     */
    public function getUpsellItems(Product $product): array
    {
        return $this->getLinkedItems($product, 'upsell');
    }

    /**
     * @param Product|ProductInterface $product
     * @param string $linkType
     * @return ProductInterface[]
     */
    public function getLinkedItems(Product $product, string $linkType): array
    {
        return $this->addFilter('sku', $this->getLinkedSkus($product, $linkType), 'in')->getItems();
    }

    /**
     * @param Product|ProductInterface $product
     * @param string $linkType
     * @return ProductInterface[]
     */
    private function getLinkedSkus(Product $product, string $linkType): array
    {
        $criteria = new ListCriteria($product->getSku(), [$linkType], $product);

        $links = $this->productLinkQuery->search([$criteria])[0];

        return map(function (ProductLinkInterface $productLink): string {
            return $productLink->getLinkedProductSku();
        }, (array) $links->getResult());
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
