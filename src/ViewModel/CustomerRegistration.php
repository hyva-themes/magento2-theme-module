<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Customer\Model\Registration;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerRegistration implements ArgumentInterface
{
    /**
     * @var Registration
     */
    protected $registration;

    /**
     * @param Registration $registration
     */
    public function __construct(
        Registration $registration
    ) {
        $this->registration = $registration;
    }

    /**
     * Check if Customer Registration is allowed
     *
     * @return bool
     */
    public function isAllowed(): bool
    {
        return $this->registration->isAllowed();
    }
}
