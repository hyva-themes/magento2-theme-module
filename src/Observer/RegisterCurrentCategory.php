<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This category is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\Event\ObserverInterface;
use Hyva\Theme\ViewModel\CurrentCategory;

/**
 * Class RegisterCurrentCategory
 *
 * Same approach of custom Product registry
 * @see \Hyva\Theme\Observer\RegisterCurrentCategory
 */
class RegisterCurrentCategory implements ObserverInterface
{
    /**
     * @var CurrentCategory
     */
    protected $currentCategory;

    public function __construct(CurrentCategory $currentCategory)
    {
        $this->currentCategory = $currentCategory;
    }

    public function execute(Event $event)
    {
        /** @var CategoryInterface $category */
        $category = $event->getData('category');
        $this->currentCategory->set($category);
    }
}
