<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Cart;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class ItemOrder implements ArgumentInterface
{
    public const XML_PATH_SHOW_NEWEST_ITEMS_FIRST = 'checkout/cart/show_newest_items_first';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isShowNewestItemsFirst(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SHOW_NEWEST_ITEMS_FIRST, ScopeInterface::SCOPE_STORE);
    }
}
