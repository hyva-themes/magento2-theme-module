<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\PageJsDependencyRegistry;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PageJsDependenciesRenderer implements ArgumentInterface
{
    /**
     * @var PageJsDependencyRegistry
     */
    private $pageJsDependencyRegistry;

    public function __construct(PageJsDependencyRegistry  $pageJsDependencyRegistry)
    {
        $this->pageJsDependencyRegistry = $pageJsDependencyRegistry;
    }

    public function getJsDependenciesHtml(): string
    {
        return $this->pageJsDependencyRegistry->getJsDependenciesHtml();
    }
}
