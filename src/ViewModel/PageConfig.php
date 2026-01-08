<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
