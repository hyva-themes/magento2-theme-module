<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Catalog\Model\Product;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class EmailToFriend implements ArgumentInterface
{
    /**
     * @var CurrentCategory
     */
    protected $currentCategory;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var StoreConfig
     */
    private $storeConfig;

    public function __construct(
        CurrentCategory $currentCategory,
        UrlInterface $urlBuilder,
        StoreConfig $storeConfig
    ) {
        $this->currentCategory = $currentCategory;
        $this->urlBuilder = $urlBuilder;
        $this->storeConfig = $storeConfig;
    }

    /**
     * Check if product can be emailed to friend
     *
     * @return bool
     */
    public function canEmailToFriend(): bool
    {
        return (bool) $this->storeConfig->getStoreConfig('sendfriend/email/enabled');
    }

    /**
     * Retrieve email to friend url
     *
     * @param Product $product
     * @return string
     */
    public function getEmailToFriendUrl(Product $product): string
    {
        $categoryId = null;
        if ($this->currentCategory->exists()) {
            $categoryId = $this->currentCategory->get()->getId();
        }
        return $this->urlBuilder->getUrl(
            'sendfriend/product/send',
            ['id' => $product->getId(), 'cat_id' => $categoryId]
        );
    }
}
