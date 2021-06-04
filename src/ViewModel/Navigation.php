<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Service\Navigation as NavigationService;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

use function array_map as map;
use function array_merge as merge;
use function array_unique as unique;
use function array_values as values;

class Navigation implements ArgumentInterface, IdentityInterface
{
    /**
     * @var NavigationService
     */
    protected $navigationService;

    private $requestedMaxLevel;

    private $cacheIdentities;

    public function __construct(NavigationService $navigationService)
    {
        $this->navigationService = $navigationService;
    }

    /**
     * @param bool|int $maxLevel
     * @return array|false
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getNavigation($maxLevel = false)
    {
        $menuTree = $this->navigationService->getMenuTree($maxLevel);

        return $this->processCacheIdentities($this->getMenuData($menuTree), $maxLevel);
    }

    /**
     * @param Node $menuTree
     * @return array
     */
    protected function getMenuData(Node $menuTree)
    {
        $children   = $menuTree->getChildren();
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
        if ($this->isNewMaxLevel($maxLevel)) {
            $this->requestedMaxLevel = $maxLevel;
            $this->cacheIdentities   = unique(merge(...values(map([$this, 'extractCacheIdentities'], $menuData))));
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
        return $this->cacheIdentities ?? [];
    }
}
