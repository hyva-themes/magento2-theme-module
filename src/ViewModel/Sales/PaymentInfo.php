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
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class PaymentInfo implements ArgumentInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
    }

    /**
     * @param OrderInterface|Order $order
     * @return string
     */
    public function getPaymentTitle($order): string
    {
        try {
            return $order->getPayment()->getMethodInstance()->getTitle();
        } catch (LocalizedException $exception) {
            $this->logger->error('Error retrieving payment method title: ' . $exception->getMessage());
        }

        return (string) $order->getPayment()->getMethod();
    }
}
