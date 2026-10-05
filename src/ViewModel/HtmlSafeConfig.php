<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class HtmlSafeConfig implements ArgumentInterface
{
    public const XML_PATH_MAP_X_HTML_SAFE_TO_X_HTML = 'hyva_theme_dev/html_safe/map_x_html_safe_to_x_html';

    public const MAP_ON_CSP_PAGES_ONLY = 'csp';
    public const MAP_ON_ALL_PAGES = 'all';
    public const MAP_DISABLED = 'disabled';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Whether x-html should be registered as an alias for x-html-safe's sanitized rendering,
     * given whether the current page is running the Alpine CSP build.
     */
    public function shouldMapXHtmlSafeToXHtml(bool $isCspBuild): bool
    {
        $setting = $this->scopeConfig->getValue(
            self::XML_PATH_MAP_X_HTML_SAFE_TO_X_HTML,
            ScopeInterface::SCOPE_STORE
        );

        if ($setting === self::MAP_ON_ALL_PAGES) {
            return true;
        }

        if ($setting === self::MAP_ON_CSP_PAGES_ONLY) {
            return $isCspBuild;
        }

        // Default (including an unset/unrecognized value): never map.
        return false;
    }
}
