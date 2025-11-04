<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Customer\CustomerData\SectionPool;
use Magento\Customer\Model\Group as CustomerGroup;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomerSectionData implements ArgumentInterface
{
    /**
     * @var SectionPool
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
        SectionPool $sectionPool,
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
         */
        $customerId = $this->customerSession->getCustomerId();
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $this->customerSession->setCustomerId(null);
        $this->customerSession->setCustomerGroupId(CustomerGroup::NOT_LOGGED_IN_ID);

        $defaultSectionData = [];
        foreach ($this->sectionPool->getSectionNames() as $key) {
            $defaultSectionData[$key] = $this->getDefaultDataForSection($key);
        }

        /*
         * Restore session
         */
        $this->customerSession->setCustomerId($customerId);
        $this->customerSession->setCustomerGroupId($customerGroupId);

        return $defaultSectionData;
    }

    private function getDefaultDataForSection(string $name): array
    {
        if (!isset($this->defaultSectionDataKeys[$name])) {
            return [];
        }
        if (true === $this->defaultSectionDataKeys[$name]) {
            return $this->sectionPool->getSectionsData([$name])[$name];
        }
        return json_decode($this->defaultSectionDataKeys[$name], true) ?? [];
    }
}
