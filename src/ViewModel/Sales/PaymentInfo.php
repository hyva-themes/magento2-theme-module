<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
        $method = (string)$order->getPayment()->getMethod();

        try {
            return $order->getPayment()->getMethodInstance()->getTitle() ?? $method;
        } catch (LocalizedException $exception) {
            $this->logger->error('Error retrieving payment method title: ' . $exception->getMessage());
        }

        return $method;
    }
}
