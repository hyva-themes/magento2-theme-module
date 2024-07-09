<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Csp\Model\Collector\DynamicCollector as DynamicCspCollector;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class InlineScript implements ArgumentInterface
{

    public function __construct(
        private DynamicCspCollector $dynamicCspCollector
    ) {
    }

    public function registerWithCsp(): void
    {
        // TODO: Do we maybe need 'strict-dynamic' for the mini-cart additional actions?
        // TODO: https://w3c.github.io/webappsec-csp/#strict-dynamic-usage

        $pageContent = ob_get_contents();
        $scriptContent = $this->extractLastScriptContent($pageContent);

        if ($scriptContent) {
            $this->dynamicCspCollector->add(
                new FetchPolicy(
                    'script-src',
                    false, /* noneAllowed */
                    [], /* hostSources */
                    [], /* schemeSources */
                    false, /* selfAllowed */
                    false, /* inlineAllowed */
                    false, /* evalAllowed*/
                    [], /* nonceValues */
                    $this->generateHashValue($scriptContent) /* hashValues */
                )
            );
        }
    }

    /**
     * Extract the last script contents in the partial page DOM.
     *
     * We don't use a regex because of expensive backtracking.
     *
     * The reason we don't use DOMDocument is that $pageContent is partial DOM tree consisting
     * only of the nodes up to the script tag, that is, many elements are unclosed.
     */
    private function extractLastScriptContent(string $pageContent): string
    {
        $trimmedPageContent = rtrim($pageContent);
        if (strtolower(substr($trimmedPageContent, -9)) !== '</script>') {
            return '';
        }
        $scriptEnd = substr($trimmedPageContent, 0, -9);
        $pos = strripos($scriptEnd, '<script>');
        if ($pos === false) {
            return '';
        }
        return substr($scriptEnd, $pos + 8);
    }

    private function generateHashValue(string $content): array
    {
        return [base64_encode(hash('sha256', $content, true)) => 'sha256'];
    }
}
