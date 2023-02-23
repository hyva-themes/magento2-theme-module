<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Sales;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

class PaymentResolver implements ArgumentInterface
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    public function __construct(
        Registry $coreRegistry,
        LoggerInterface $logger
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getPaymentInfo()
    {
        /** @var OrderInterface $order */
        $order = $this->coreRegistry->registry('current_order');

        try {
            return $order->getPayment()->getMethodInstance()->getTitle();
        } catch (LocalizedException $e) {
            $this->logger->error('There is no payment method title ' . $e->getMessage());
        }

        return $order->getPayment()->getMethod();
    }
}
