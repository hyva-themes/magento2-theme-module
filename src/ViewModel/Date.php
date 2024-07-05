<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Date implements ArgumentInterface
{
    public function __construct()
    {
    }

    /**
     * Get input date or the current date in UTC timezone ('Y-m-d')
     *
     * @param string|null $date
     * @return string
     */
    public function getDateYMD(?string $date): string
    {
        return $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
    }
}
