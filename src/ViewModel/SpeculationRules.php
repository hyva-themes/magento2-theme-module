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
     * @var array
     */
    private $excludeList;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        array $excludeFromPreloading = []
    ) {
        $this->scopeConfig = $scopeConfig;
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
        $defaultExcludes = keys(filter($this->excludeList));
        $allExcludes = merge($defaultExcludes, $excludes);

        $excludePatterns = [];

        foreach ($allExcludes as $value) {
            if (empty(trim((string) $value))) {
                continue;
            }

            if (strpos($value, '/') !== false || strpos($value, '*') !== false) {
                $excludePatterns[] = $value;
            } elseif (substr($value, 0, 1) === '.') {
                $excludePatterns[] = '*' . $value;
            } else {
                $excludePatterns[] = '/' . $value . '/*';
                $excludePatterns[] = '*/' . $value . '/*';
            }
        }

        return $excludePatterns;
    }
}
