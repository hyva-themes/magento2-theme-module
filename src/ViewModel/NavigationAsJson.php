<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
