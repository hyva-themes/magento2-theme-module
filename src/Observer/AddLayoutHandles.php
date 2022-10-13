<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\EntitySpecificHandlesList;
use Magento\Framework\View\Layout;

/**
 * This observer adds layout handles with the hyva_ prefix if a Hyva theme is selected. This way, modules can be written
 * in a way to support classical frontend ("luma") and hyva simultaneously.
 *
 * We use an observer instead of a plugin to {@see \Magento\Framework\View\Layout::generateElements()}, because we
 * believe events are the "correct" way over plugins if they are available (less coupling, simpler code)
 *
 * In consequence there are some edge cases, where Magento doesn't use the layout builder which triggers the event
 * and calls {@see \Magento\Framework\View\Layout::generateXml()} and
 * {@see \Magento\Framework\View\Layout::generateElements()} directly instead.
 *
 * In Magento 2.4.1 Open Source, these are:
 * - area emulation for emails
 * - some dead code in the onepage checkout to render HTML in AJAX calls (Magento 1 artifacts)
 * - the product list widget
 * - the price renderer block
 *
 * @event layout_load_before
 * @see \Magento\Framework\View\Layout\Builder::generateLayoutBlocks()
 */
class AddLayoutHandles implements ObserverInterface
{
    /**
     * @var CurrentTheme
     */
    protected $theme;

    /**
     * @var EntitySpecificHandlesList
     */
    private $entitySpecificHandlesList;

    public function __construct(CurrentTheme $theme, EntitySpecificHandlesList $entitySpecificHandlesList)
    {
        $this->theme                     = $theme;
        $this->entitySpecificHandlesList = $entitySpecificHandlesList;
    }

    public function execute(Observer $observer)
    {
        /** @var Layout $layout */
        $layout = $observer->getData('layout');
        if ($this->theme->isHyva()) {
            foreach ($layout->getUpdate()->getHandles() as $handle) {
                if (strpos($handle, 'hyva_') !== 0) {
                    $layout->getUpdate()->addHandle("hyva_$handle");
                }
            }
        }
        foreach ($this->entitySpecificHandlesList->getHandles() as $handle) {
            if (strpos($handle, 'hyva_') !== 0) {
                $this->entitySpecificHandlesList->addHandle("hyva_$handle");
            }
        }
    }
}
