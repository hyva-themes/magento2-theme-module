<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Backwards compatible wrapper of \Magento\Customer\ViewModel\LoginButton for Magento < 2.4.5
 */
class LoginButton implements ArgumentInterface
{
    /**
     * @var ?\Magento\Customer\ViewModel\LoginButton
     */
    private $delegate;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        if (class_exists(\Magento\Customer\ViewModel\LoginButton::class)) {
            $this->delegate = $objectManager->create(\Magento\Customer\ViewModel\LoginButton::class);
        }
    }

    /**
     * Delegate to the original class if it exists because the method is a customization hook for plugins
     */
    public function disabled(): bool
    {
        return $this->delegate ? $this->delegate->disabled() : false;
    }
}
