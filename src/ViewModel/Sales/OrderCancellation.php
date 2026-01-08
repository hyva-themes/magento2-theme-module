<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Sales;

use Hyva\Theme\ViewModel\StoreConfig;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\OrderCancellationUi\ViewModel\Config as OrderCancellationViewModel;

class OrderCancellation implements ArgumentInterface
{

    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @var StoreConfig
     */
    private $storeConfig;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Do not depend on modules from Magento_OrderCancellationUi since it may be disabled or not present in the current Magento version.
     */
    public function __construct(
        ModuleListInterface $moduleList,
        StoreConfig $storeConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->moduleList = $moduleList;
        $this->storeConfig = $storeConfig;
        $this->objectManager = $objectManager;
    }

    public function isEnabled(): bool
    {
        return $this->moduleList->has('Magento_OrderCancellationUi') && $this->storeConfig->getStoreConfig('sales/cancellation/enabled');
    }

    public function canCancel($orderId): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        // Here we are sure the Magento_OrderCancellationUi module is present and enabled

        /** @var OrderCancellationViewModel $orderCancellation */
        $orderCancellation = $this->objectManager->get(OrderCancellationViewModel::class);

        return $orderCancellation->canCancel((int) $orderId);
    }
}
