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
 * Backwards compatible wrapper of \Magento\Customer\ViewModel\CreateAccountButton for Magento < 2.4.5
 */
class CreateAccountButton implements ArgumentInterface
{
    /**
     * @var ?\Magento\Customer\ViewModel\CreateAccountButton
     */
    private $delegate;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        if (class_exists(\Magento\Customer\ViewModel\CreateAccountButton::class)) {
            $this->delegate = $objectManager->create(\Magento\Customer\ViewModel\CreateAccountButton::class);
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
