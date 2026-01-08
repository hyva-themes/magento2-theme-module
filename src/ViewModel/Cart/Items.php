<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
