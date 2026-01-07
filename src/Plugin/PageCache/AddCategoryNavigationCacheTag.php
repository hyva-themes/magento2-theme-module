<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageCache;

use Hyva\Theme\ViewModel\Navigation;
use Magento\Catalog\Model\Category;

class AddCategoryNavigationCacheTag
{
    /**
     * Add hyva navigation cache tag to category identities in admin so the top navigation cache is cleaned.
     *
     * @param Category $subject
     * @param string[] $result
     * @return string[]
     */
    public function afterGetIdentities(Category $subject, $result)
    {
        $result[] = Navigation::CACHE_TAG;
        return $result;
    }
}
