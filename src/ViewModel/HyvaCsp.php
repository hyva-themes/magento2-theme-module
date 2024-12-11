<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\HtmlPageContent;
use Magento\Csp\Helper\CspNonceProvider;
use Magento\Csp\Model\Collector\DynamicCollector as DynamicCspCollector;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\Cache\Type as FullPageCache;

// TODO: Do we maybe need 'strict-dynamic' for the mini-cart additional actions?
// TODO: https://w3c.github.io/webappsec-csp/#strict-dynamic-usage
class HyvaCsp implements ArgumentInterface
{
    /**
     * @var DynamicCspCollector
     */
    private $dynamicCspCollector;

    /**
     * @var CspNonceProvider
     */
    private $cspNonceProvider;

    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var HtmlPageContent
     */
    private $htmlPageContent;

    /**
     * @var CacheState
     */
    private $cacheState;

    public function __construct(
        DynamicCspCollector $dynamicCspCollector,
        CspNonceProvider $cspNonceProvider,
        HtmlPageContent $htmlPageContent,
        LayoutInterface $layout,
        CacheState $cacheState
    ) {
        $this->dynamicCspCollector = $dynamicCspCollector;
        $this->layout = $layout;
        $this->htmlPageContent = $htmlPageContent;
        $this->cspNonceProvider = $cspNonceProvider;
        $this->cacheState = $cacheState;
    }

    public function registerInlineScript(): void
    {
        if ($this->cacheState->isEnabled(FullPageCache::TYPE_IDENTIFIER) && $this->layout->isCacheable()) {
            $this->addInlineScriptHashToCspHeader();
        } else {
            $this->addCspNonceToInlineScript();
        }
    }

    private function generateHashValue(string $content): array
    {
        return [base64_encode(hash('sha256', $content, true)) => 'sha256'];
    }

    private function addInlineScriptHashToCspHeader(): void
    {
        $pageContent = rtrim(ob_get_contents());
        $scriptContent = $this->htmlPageContent->extractLastElementContent($pageContent, 'script');

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

    private function addCspNonceToInlineScript(): void
    {
        $pageContent = rtrim(ob_get_contents());
        $script = $this->htmlPageContent->extractLastElement($pageContent, 'script');

        if ($script) {
            $openingScriptTag = $this->htmlPageContent->getFirstTag($script);

            // Reset the output buffer
            ob_clean();
            // Add the page content up to the script tag to the output buffer
            echo substr($pageContent, 0, strlen($script) * -1);
            // Add the script tag with nonce attribute to the output buffer
            echo $this->htmlPageContent->injectAttribute($openingScriptTag, 'nonce', $this->cspNonceProvider->generateNonce());
            // Add the script content and the closing script tag to the output buffer
            echo substr($script, strlen($openingScriptTag));
        }
    }
}
