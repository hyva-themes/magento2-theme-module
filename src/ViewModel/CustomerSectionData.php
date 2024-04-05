<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Customer\CustomerData\SectionPoolInterface;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use function array_keys as keys;

class CustomerSectionData implements ArgumentInterface
{
    /**
     * @var SectionPoolInterface
     */
    private $sectionPool;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var array
     */
    private $defaultSectionDataKeys;

    public function __construct(
        SectionPoolInterface $sectionPool,
        CustomerSession $customerSession,
        array $defaultSectionDataKeys = []
    ) {
        $this->sectionPool = $sectionPool;
        $this->customerSession = $customerSession;
        $this->defaultSectionDataKeys = $defaultSectionDataKeys;
    }

    /**
     * Return default section data.
     *
     * All sections are emptied except for those explicitly configured to be included in the default section data on cached pages.
     *
     * @return array[]
     */
    public function getDefaultSectionData(): array
    {
        /*
         * Ensure no customer specific data is returned by $sectionPool->getSectionsData().
         * Aside from security considerations, Magento_InstantPurchase causes issues otherwise.
         */
        $customerId = $this->customerSession->getCustomerId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $this->customerSession->setCustomerId(null);
        $this->customerSession->setCustomerGroupId(CustomerGroup::NOT_LOGGED_IN_ID);

        $sectionData = $this->sectionPool->getSectionsData() ?: [];

        /*
         * Restore session
         */
        $this->customerSession->setCustomerId($customerId);
        $this->customerSession->setCustomerGroupId($customerGroupId);

        return $this->cleanCustomerSectionData($sectionData);
    }

    private function cleanCustomerSectionData(array $sectionData): array
    {
        foreach (keys($sectionData) as $key) {
            if (!isset($this->defaultSectionDataKeys[$key])) {
                $sectionData[$key] = [];
            } elseif (true !== $this->defaultSectionDataKeys[$key]) {
                $sectionData[$key] = json_decode($this->defaultSectionDataKeys[$key], true) ?? [];
            }
        }
        return $sectionData;
    }
}
