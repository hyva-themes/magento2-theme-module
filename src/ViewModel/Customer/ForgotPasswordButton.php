<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Backwards compatible wrapper of \Magento\Customer\ViewModel\ForgotPasswordButton for Magento < 2.4.5
 */
class ForgotPasswordButton implements ArgumentInterface
{
    /**
     * @var ?\Magento\Customer\ViewModel\ForgotPasswordButton
     */
    private $delegate;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        if (class_exists(\Magento\Customer\ViewModel\ForgotPasswordButton::class)) {
            $this->delegate = $objectManager->create(\Magento\Customer\ViewModel\ForgotPasswordButton::class);
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
