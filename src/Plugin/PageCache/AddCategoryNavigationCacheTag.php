<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
