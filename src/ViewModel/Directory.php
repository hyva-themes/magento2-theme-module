<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\Store;

class Directory implements ArgumentInterface
{
    /**
     * @var DirectoryHelper
     */
    private $directoryHelper;

    public function __construct(DirectoryHelper $directoryHelper)
    {
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * Return default country code from system configuration at general/country/default
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getDefaultCountry($store = null): string
    {
        return $this->directoryHelper->getDefaultCountry($store);
    }

    /**
     * Return array of ISO2 country codes set in system configuration at general/country/destinations
     *
     * @return string[]
     */
    public function getTopCountryCodes(): array
    {
        return $this->directoryHelper->getTopCountryCodes();
    }
}
