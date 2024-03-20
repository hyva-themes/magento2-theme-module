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
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use function array_keys as keys;

class CustomerSectionData implements ArgumentInterface
{
    /**
     * @var SectionPoolInterface
     */
    private $sectionPool;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var array
     */
    private $defaultSectionDataKeys;

    public function __construct(
        SectionPoolInterface $sectionPool,
        LayoutInterface $layout,
        array $defaultSectionDataKeys = []
    ) {
        $this->sectionPool = $sectionPool;
        $this->layout = $layout;
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
        return $this->layout->isCacheable()
            ? $this->getCleanedDefaultSectionData()
            : [];
    }

    private function getCleanedDefaultSectionData(): array
    {
        $sectionData = $this->sectionPool->getSectionsData() ?: [];
        foreach (keys($sectionData) as $key) {
            if (! isset($this->defaultSectionDataKeys[$key])) {
                $sectionData[$key] = [];
            } elseif (true !== $this->defaultSectionDataKeys[$key]) {
                $sectionData[$key] = json_decode($this->defaultSectionDataKeys[$key]) ?? [];
            }
        }

        return $sectionData;
    }
}
