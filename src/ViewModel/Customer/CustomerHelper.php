<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerHelper implements ArgumentInterface
{
    protected $customerRepositoryInterface;

    public function __construct(CustomerRepositoryInterface $customerRepositoryInterface)
    {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
    }

    /**
     * Get Customer by Customer ID
     */
    private function getCustomerById(int $id): ?object
    {
        $customer = $this->customerRepositoryInterface->getById($id);
        return $customer;
    }

    /**
     * Get Customer Email by Customer ID
     */
    public function getEmailById(int $id): string
    {
        $customer = $this->getCustomerById($id);
        return $customer ? $customer->getEmail() : '';
    }
}
