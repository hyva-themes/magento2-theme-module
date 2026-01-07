<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class Date implements ArgumentInterface
{
    /**
     * Get input date or the current date in UTC timezone ('Y-m-d')
     *
     * @param string|null $date
     * @return string
     */
    public function getDateYMD(?string $date = null): string
    {
        return $date ? date('Y-m-d', strtotime($date)) : date('Y-m-d');
    }
}
