<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Wishlist\Helper\Data as WishlistHelper;

class Wishlist implements ArgumentInterface
{
    /**
     * @var WishlistHelper
     */
    private $wishlistHelper;

    public function __construct(WishlistHelper $wishlistHelper)
    {
        $this->wishlistHelper = $wishlistHelper;
    }

    public function isEnabled(): bool
    {
        return $this->wishlistHelper->isAllow();
    }

    public function isAllowInCart(): bool
    {
        return $this->wishlistHelper->isAllowInCart();
    }
}
