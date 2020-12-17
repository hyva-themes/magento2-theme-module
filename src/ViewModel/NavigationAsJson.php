<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Data\Tree\Node\Collection;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class NavigationAsJson extends Navigation
{
    /**
     * @return false|string
     */
    public function getNavigationAsJson()
    {
        return \json_encode($this->getNavigation());
    }
}
