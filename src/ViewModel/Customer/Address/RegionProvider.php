<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer\Address;

use Magento\Directory\Helper\Data as RegionHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Backwards compatible wrapper of \Magento\Customer\ViewModel\Address\RegionProvider for Magento < 2.4.5
 */
class RegionProvider implements ArgumentInterface
{
    /**
     * @var ?\Magento\Customer\ViewModel\Address\RegionProvider
     */
    private $delegate;

    /**
     * @var RegionHelper
     */
    private $regionHelper;

    /**
     * @var JsonSerializer
     */
    private $json;

    /**
     * @var ?array[]
     */
    private $regions;

    public function __construct(ObjectManagerInterface $objectManager, RegionHelper $regionHelper, JsonSerializer $json)
    {
        if (class_exists(\Magento\Customer\ViewModel\Address\RegionProvider::class)) {
            $this->delegate = $objectManager->create(\Magento\Customer\ViewModel\Address\RegionProvider::class);
        }
        $this->regionHelper = $regionHelper;
        $this->json         = $json;
    }

    /**
     * Delegate to the original class if it exists because the method is a customization hook for plugins
     */
    public function getRegionJson(): string
    {
        return $this->delegate ? $this->delegate->getRegionJson() : $this->json->serialize($this->getRegions());
    }

    /**
     * @see \Magento\Directory\Model\RegionProvider::getRegions
     */
    private function getRegions() : array
    {
        if (!$this->regions) {
            $regions = $this->regionHelper->getRegionData();
            $this->regions['config'] = $regions['config'];
            unset($regions['config']);
            foreach ($regions as $countryCode => $countryRegions) {
                foreach ($countryRegions as $regionId => $regionData) {
                    $this->regions[$countryCode][] = [
                        'id'   => $regionId,
                        'name' => $regionData['name'],
                        'code' => $regionData['code']
                    ];
                }
            }
        }
        return $this->regions;
    }
}
