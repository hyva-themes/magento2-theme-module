<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use function array_map as map;
use function array_merge as merge;
use function array_unique as unique;
use function array_values as values;
use Hyva\Theme\Service\Navigation as NavigationService;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Navigation implements ArgumentInterface, IdentityInterface
{
    /**
     * Cache tag to use instead of category tags  if more than 200 categories are rendered in the navigation.
     */
    public const CACHE_TAG = 'hyva_nav';

    /**
     * @var NavigationService
     */
    private $navigationService;

    /**
     * @var bool|int
     */
    private $requestedMaxLevel;

    /**
     * @var string[]
     */
    private $cacheIdentities;

    /**
     * The maximum number of category cache identities to return before using a single hyva_nav cache tag instead.
     *
     * @var int
     */
    private $maxCategoryCacheTags;

    /**
     * Save Navigation cache so if used twice with the same settings resolve the same data
     *
     * @var array
     */
    private $requestLevelCache = [];

    public function __construct(NavigationService $navigationService, int $maxCategoryCacheTags = 200)
    {
        $this->navigationService = $navigationService;
        $this->maxCategoryCacheTags = $maxCategoryCacheTags;
    }

    /**
     * @param bool|int $maxLevel
     * @return array|false
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNavigation($maxLevel = false)
    {
        $maxLevel = (int) $maxLevel;
        if (isset($this->requestLevelCache[$maxLevel])) {
            return $this->requestLevelCache[$maxLevel];
        }

        $menuTree = $this->navigationService->getMenuTree($maxLevel);

        return $this->requestLevelCache[$maxLevel] = $this->processCacheIdentities($this->getMenuData($menuTree), $maxLevel);
    }

    private function flattenTree(array $categories, array $acc = []): array
    {
        foreach ($categories as $category) {
            $id = substr($category['id'], strrpos($category['id'], '-') + 1);
            $acc['c' . $id] = [
                'url' => $category['url'],
                'name' => $category['name'],
                'path' => $category['path'] ?? '',
            ];
            $acc = $this->flattenTree($category['childData'] ?? [], $acc);
        }
        return $acc;
    }

    public function getCategories($maxLevel = false): array
    {
        return $this->flattenTree($this->getNavigation($maxLevel));
    }

    /**
     * @param Node $menuTree
     * @return array
     */
    protected function getMenuData(Node $menuTree)
    {
        $children = $menuTree->getChildren();
        $childLevel = $this->getChildLevel($menuTree->getLevel());
        $this->removeChildrenWithoutActiveParent($children, $childLevel);
        $parentPositionClass = $menuTree->getPositionClass();

        $output = [];

        /** @var Node $child */
        foreach ($children as $child) {
            $child->setPosition($parentPositionClass);
            $child->setData('childData', $this->addSubMenu($child));
            $output[$child->getId()] = ($child->getData());
        }

        return $output;
    }

    /**
     * @param $child
     * @return array
     */
    protected function addSubMenu($child)
    {
        if (!$child->hasChildren()) {
            return [];
        }

        return $this->getMenuData($child);
    }

    /**
     * @param Collection $children
     * @param int $childLevel
     */
    protected function removeChildrenWithoutActiveParent(Collection $children, int $childLevel): void
    {
        /** @var Node $child */
        foreach ($children as $child) {
            if ($childLevel === 0 && $child->getData('is_parent_active') === false) {
                $children->delete($child);
            }
        }
    }

    /**
     * @param $parentLevel
     * @return int
     */
    protected function getChildLevel($parentLevel): int
    {
        return $parentLevel === null ? 0 : $parentLevel + 1;
    }

    private function processCacheIdentities(array $menuData, $maxLevel): array
    {
        if ($this->isNewMaxLevel($maxLevel) && !empty($menuData)) {
            $this->requestedMaxLevel = $maxLevel;
            $this->cacheIdentities = unique(merge(...values(map([$this, 'extractCacheIdentities'], $menuData))));
        }
        return map([$this, 'removeCacheIdentities'], $menuData);
    }

    private function isNewMaxLevel($maxLevel): bool
    {
        return !isset($this->cacheIdentities) || ( // this is the first request
                $this->requestedMaxLevel !== false && // previous requests where not unlimited
                $maxLevel > $this->requestedMaxLevel // this request has a higher limit than previous ones
            );
    }

    private function extractCacheIdentities(array $menuData): array
    {
        $identities = $menuData['identities'] ?? [];
        return merge($identities, ...values(map([$this, 'extractCacheIdentities'], $menuData['childData'] ?? [])));
    }

    private function removeCacheIdentities(array $menuData): array
    {
        $menuData['childData'] = map([$this, 'removeCacheIdentities'], $menuData['childData'] ?? []);
        unset($menuData['identities']);

        return $menuData;
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {

        return $this->cacheIdentities && count($this->cacheIdentities) > $this->maxCategoryCacheTags
            ? [self::CACHE_TAG]
            : $this->cacheIdentities ?? [];
    }
}
