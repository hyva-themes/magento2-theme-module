<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Customer implements ArgumentInterface
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;

    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        HttpContext $httpContext
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->httpContext = $httpContext;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    private function getCustomerById(int $id): CustomerInterface
    {
        return $this->customerRepositoryInterface->getById($id);
    }

    public function getEmailById(int $id): string
    {
        $customer = $this->getCustomerById($id);
        return $customer ? $customer->getEmail() : '';
    }
}
