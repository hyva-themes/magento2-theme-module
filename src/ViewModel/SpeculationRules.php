<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class SpeculationRules implements ArgumentInterface
{
    public const DEFAULT_EXCLUDE_LIST = [
        'customer',
        'search',
        'sales',
        'wishlist',
        'checkout',
        'paypal',
    ];

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    protected function getSpeculationConfig(string $attribute): mixed
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

    /**
     * Build the speculation rule structure for the 'not' condition based on a list of exclusion paths.
     *
     * This function processes an array of strings and converts them into valid 'href_matches' patterns.
     * - Plain strings ('customer') are converted to path patterns ('/customer/\*' and '\*\/customer/\*').
     * - Strings starting with a dot ('.pdf') are treated as file extensions and converted to wildcard patterns ('*.pdf').
     * - Strings already containing a '/' or '*' are used as-is.
     */
    public function getExcludeRules(array $excludes): array
    {
        $defaultExcludes = self::DEFAULT_EXCLUDE_LIST;
        $excludes = array_merge($defaultExcludes, $excludes);

        $excludePatterns = [];

        foreach ($excludes as $value) {
            if (empty(trim((string) $value))) {
                continue;
            }

            if (str_contains($value, '/') || str_contains($value, '*')) {
                $excludePatterns[] = $value;
            } elseif (str_starts_with($value, '.')) {
                $excludePatterns[] = '*' . $value;
            } else {
                $excludePatterns[] = '/' . $value . '/*';
                $excludePatterns[] = '*/' . $value . '/*';
            }
        }

        return $excludePatterns;
    }
}
