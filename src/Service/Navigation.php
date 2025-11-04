<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 *
 * This file includes sections of software developed by Magento, Inc. (https://github.com/magento/magento2)
 * and distributed under the Academic Free License (AFL) v3.0.
 */

declare(strict_types=1);

namespace Hyva\Theme\Service;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryColleciton;
use Magento\Catalog\Model\ResourceModel\Category\StateDependentCollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

/** @see \Magento\Catalog\Plugin\Block\Topmenu */
class Navigation
{
    /**
     * Catalog category
     *
     * @var \Magento\Catalog\Helper\Category
     */
    protected $catalogCategory;

    /**
     * @var StateDependentCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Resolver
     */
    protected $layerResolver;

    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * @var TreeFactory
     */
    protected $treeFactory;

    protected $menu;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Catalog\Helper\Category $catalogCategory
     * @param StateDependentCollectionFactory $categoryCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param NodeFactory $nodeFactory
     * @param TreeFactory $treeFactory
     * @param Resolver $layerResolver
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $catalogCategory,
        StateDependentCollectionFactory $categoryCollectionFactory,
        StoreManagerInterface $storeManager,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Resolver $layerResolver
    ) {
        $this->catalogCategory = $catalogCategory;
        $this->collectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        $this->layerResolver = $layerResolver;
        $this->nodeFactory = $nodeFactory;
        $this->treeFactory = $treeFactory;
    }

    /**
     * Build category tree for menu block.
     *
     * @param int $maxLevel
     * @return Node
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings("PMD.UnusedFormalParameter")
     */
    public function getMenuTree($maxLevel)
    {
        $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getId();

        $collection = $this->getCategoryTree($storeId, $rootId, $maxLevel);
        $currentCategory = $this->getCurrentCategory();
        $mapping = [$rootId => $this->getMenu()];
        foreach ($collection as $category) {
            $categoryParentId = $category->getParentId();
            if (!isset($mapping[$categoryParentId])) {
                $parentIds = $category->getParentIds();
                foreach ($parentIds as $parentId) {
                    if (isset($mapping[$parentId])) {
                        $categoryParentId = $parentId;
                    }
                }
            }

            $parentCategoryNode = $mapping[$categoryParentId];

            $categoryNode = new Node(
                $this->getCategoryAsArray(
                    $category,
                    $currentCategory,
                    $category->getParentId() == $categoryParentId
                ),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);

            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }

        return $this->getMenu();
    }

    /**
     * Get Category Tree
     *
     * @param int $storeId
     * @param int $rootId
     * @param int $maxLevel
     * @return CategoryColleciton
     * @throws LocalizedException
     */
    public function getCategoryTree($storeId, $rootId, $maxLevel = 0)
    {
        /** @var CategoryColleciton $collection */
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect(['name', 'image']);
        $collection->addFieldToFilter('path', ['like' => '1/' . $rootId . '/%']); //load only from store root
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        if ($maxLevel > 0) {
            $collection->addLevelFilter($maxLevel);
        } else {
            $collection->addNavigationMaxDepthFilter();
        }
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }

    /**
     * Get current Category from catalog layer
     *
     * @return Category
     */
    protected function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    protected function getMenu(): Node
    {
        if (!$this->menu) {
            $this->menu = $this->nodeFactory->create(
                [
                    'data' => [],
                    'idField' => 'root',
                    'tree' => $this->treeFactory->create(),
                ]
            );
        }
        return $this->menu;
    }

    /**
     * Convert category to array
     *
     * @param Category $category
     * @param Category $currentCategory
     * @param bool $isParentActive
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryAsArray($category, $currentCategory, $isParentActive)
    {
        $categoryId = $category->getId();
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $categoryId,
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'image' => $category->getImageUrl(),
            'has_active' => in_array((string)$categoryId, $currentCategory->getPathIds(), true),
            'is_active' => $categoryId == $currentCategory->getId(),
            'is_category' => true,
            'is_parent_active' => $isParentActive,
            'position' => $category->getData('position'),
            'identities' => $category->getIdentities(),
            'path' => $category->getPath()
        ];
    }
}
