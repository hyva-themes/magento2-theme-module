<?php

/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Layout\Element as LayoutElement;

/**
 * This observer adds layout handles updates with the hyva_ prefix if a Hyva theme is selected.
 * This way, modules can be written in a way to support classical frontend ("luma") and hyva simultaneously.
 * @see \Hyva\Theme\Observer\AddLayoutHandles
 *
 * @event load_file_layout_updates_xml_after
 * @see \Hyva\Theme\Framework\View\Model\Layout\Merge::_loadFileLayoutUpdatesXml
 */
class AddLayoutHandlesUpdates implements ObserverInterface
{
    /**
     * @var CurrentTheme
     */
    protected $theme;

    public function __construct(CurrentTheme $theme)
    {
        $this->theme = $theme;
    }

    public function execute(Observer $observer)
    {
        if ($this->theme->isHyva()) {
            /** @var LayoutElement $layoutXml */
            $layoutXml = $observer->getData('layoutXml');
            foreach ($layoutXml->xpath('/layouts/handle/update') as $update) {
                $hyvaUpdate = clone $update;
                $hyvaUpdate->setAttribute('handle', 'hyva_' . $update->getAttribute('handle'));
                $update->getParent()->appendChild($hyvaUpdate);
            }
        }
    }
}
