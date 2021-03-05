<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;

class CurrentCategory implements ArgumentInterface
{
    /**
     * @var CategoryInterface
     */
    protected $currentCategory;

    /**
     * @var CategoryInterfaceFactory
     */
    protected $categoryFactory;

    public function __construct(CategoryInterfaceFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
    }

    public function set(CategoryInterface $category): void
    {
        $this->currentCategory = $category;
    }

    /**
     * @return CategoryInterface
     * @throws LocalizedException
     */
    public function get(): CategoryInterface
    {
        if ($this->exists()) {
            return $this->currentCategory;
        }
        throw new LocalizedException(__('Category is not set on CategoryRegistry.'));
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return ($this->currentCategory && $this->currentCategory->getId());
    }
}
