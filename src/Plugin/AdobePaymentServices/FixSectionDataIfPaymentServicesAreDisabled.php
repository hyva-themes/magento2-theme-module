<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\AdobePaymentServices;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Event\Observer as Event;
use Magento\PaymentServicesPaypal\Observer\AddCheckoutComponents as AddPaymentServicesSmartButtonsObserver;

/**
 * There is a bug in 2.9.0 of payment services causing the section data generation to fail if there is no active quote.
 * The bug is triggered by fetching the checkout config in the smart_buttons_minicart.phtml template.
 * As a workaround, as long as `payment/payment_services/active` is false, this plugin prevents that template from rendering.
 */
class FixSectionDataIfPaymentServicesAreDisabled
{
    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        CurrentTheme $currentTheme,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->currentTheme = $currentTheme;
        $this->scopeConfig = $scopeConfig;
    }

    public function aroundExecute(AddPaymentServicesSmartButtonsObserver $subject, callable $proceed, Event $event): void
    {
        // On Hyvä based store views we skip payment services smart buttons unless payment services are enabled
        if (! $this->currentTheme->isHyva() || $this->isPaymentServicesEnabled()) {
            $proceed($event);
        }
    }

    private function isPaymentServicesEnabled()
    {
        return $this->scopeConfig->getValue('payment/payment_services/active', 'store');
    }
}
