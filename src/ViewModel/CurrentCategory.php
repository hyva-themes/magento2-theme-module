<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Api\Data\CategoryInterfaceFactory;

class CurrentCategory implements ArgumentInterface, IdentityInterface
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
     * A convenient alternative to get() that doesn't throw if there is no current category
     */
    public function fetch(): ?CategoryInterface
    {
        return $this->exists() ? $this->get() : null;
    }

    /**
     * @return CategoryInterface
     * @throws \RuntimeException
     */
    public function get(): CategoryInterface
    {
        if ($this->exists()) {
            return $this->currentCategory;
        }
        throw new \RuntimeException('Category is not set on CategoryRegistry.');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return ($this->currentCategory && $this->currentCategory->getId());
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return $this->exists() && $this->currentCategory instanceof IdentityInterface
            ? $this->currentCategory->getIdentities()
            : [];
    }
}
