<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Items implements ArgumentInterface
{
    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Items constructor.
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * return \Magento\Quote\Model\Quote\Item[]
     */
    public function getCartItems()
    {
        return $this->checkoutSession->getQuote()->getAllVisibleItems();
    }

    public function getCartItemsSkus(): string
    {
        $items = $this->getCartItems();
        $skus = [];

        foreach ($items as $item) {
            $skus[] = $item->getSku();
        }

        return implode(',', $skus);
    }
}
