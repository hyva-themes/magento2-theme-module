<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Date implements ArgumentInterface
{
    private TimezoneInterface $timezone;

    public function __construct(TimezoneInterface $timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * Get input date or the current date in UTC timezone ('Y-m-d')
     *
     * @param string|null $date
     * @return string
     */
    public function getDateYMD(?string $date = null): string
    {
        if (!$date) {
            return date('Y-m-d');
        }

        // locale aware parsing (i.e. day-first vs month-first formats like en_GB vs en_US)
        $timestamp = $this->timezone->date($date, null, false, false)->getTimestamp();

        return date('Y-m-d', $timestamp);
    }
}
