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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Navigation implements ArgumentInterface
{
    /**
     * @var NavigationService
     */
    protected $navigationService;

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

        return $this->getMenuData($menuTree);
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
}
