<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageBuilder;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\PageBuilder\Model\Stage\Config as PageBuilderConfig;

class PageBuilderConfigPlugin
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function afterGetConfig(PageBuilderConfig $subject, array $result): array
    {
        $result['background_lazy_load_default'] = (bool) $this->scopeConfig->getValue('hyva_theme_pagebuilder/images/background_lazy_load_default');

        return $result;
    }
}
