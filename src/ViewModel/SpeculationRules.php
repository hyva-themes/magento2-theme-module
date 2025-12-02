<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use function array_filter as filter;
use function array_keys as keys;
use function array_merge as merge;

class SpeculationRules implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var array
     */
    private $excludeList;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UrlInterface $urlBuilder,
        array $excludeFromPreloading = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->urlBuilder = $urlBuilder;
        $this->excludeList = $excludeFromPreloading;
    }

    protected function getSpeculationConfig(string $attribute)
    {
        $path = sprintf('hyva_theme_general/speculation_rules/%s', $attribute);
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }

    public function getMethod(): string
    {
        return (string)$this->getSpeculationConfig('method');
    }

    public function getEagerness(): string
    {
        return (string)$this->getSpeculationConfig('eagerness');
    }

    public function getStoreCodeUrlPrefix(): string
    {
        $baseUrl = $this->urlBuilder->getBaseUrl();
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        return rtrim(parse_url($baseUrl, PHP_URL_PATH) ?? '', '/');
    }

    /**
     * Build the speculation rule structure for the 'not' condition based on a list of exclusion paths.
     *
     * This function processes an array of strings and converts them into valid 'href_matches' patterns.
     * - Plain strings ('customer') are converted to path pattern ('/customer/*' or if the url has a suffix '/suffix/customer/*').
     * - Strings starting with a dot ('.pdf') are treated as file extensions and converted to wildcard patterns ('*.pdf').
     * - Strings already containing a '*' are used as-is.
     */
    public function getExcludeRules(array $excludes): array
    {
        $urlPrefix = $this->getStoreCodeUrlPrefix();
        $defaultExcludes = keys(filter($this->excludeList));
        $allExcludes = merge($defaultExcludes, $excludes);
        $excludePatterns = [];

        foreach ($allExcludes as $value) {
            $value = trim((string)$value);
            if (empty($value)) {
                continue;
            }

            if (substr($value, 0, 1) === '.') {
                $excludePatterns[] = '*' . $value;
            } elseif (strpos($value, '*') !== false) {
                $excludePatterns[] = $value;
            } else {
                $pattern = '/' . trim($value, '/') . '/*';
                $excludePatterns[] = $urlPrefix . $pattern;
            }
        }

        return $excludePatterns;
    }
}
