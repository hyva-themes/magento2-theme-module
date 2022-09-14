<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Page\Config;

class PageConfig implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $pageConfig;

    public function __construct(
        Config $pageConfig
    ) {
        $this->pageConfig = $pageConfig;
    }

    public function getPageLayout(): string
    {
        return $this->getPageConfig()->getPageLayout() ?? '';
    }

    public function getPageConfig(): Config
    {
        return $this->pageConfig;
    }
}
