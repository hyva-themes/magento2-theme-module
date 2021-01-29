<?php

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
}

