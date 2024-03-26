<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
