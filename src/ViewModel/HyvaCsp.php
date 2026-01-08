<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\HtmlPageContent;
use Magento\Csp\Api\Data\PolicyInterface;
use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Helper\CspNonceProvider;
use Magento\Csp\Model\Collector\DynamicCollector as DynamicCspCollector;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\Cache\StateInterface as CacheState;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\Cache\Type as FullPageCache;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
// phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput

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
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var HtmlPageContent
     */
    private $htmlPageContent;

    /**
     * @var CacheState
     */
    private $cacheState;

    /**
     * @var PolicyCollectorInterface
     */
    private $policyCollector;

    /**
     * @var AppState
     */
    private $appState;

    /**
     * @var string|null
     */
    private $memoizedAreaCode;

    /**
     * @var array
     */
    private $memoizedPolicies;

    public function __construct(
        DynamicCspCollector $dynamicCspCollector,
        PolicyCollectorInterface $policyCollector,
        CspNonceProvider $cspNonceProvider,
        HtmlPageContent $htmlPageContent,
        ObjectManagerInterface $objectManager,
        CacheState $cacheState,
        ?AppState $appState = null
    ) {
        $this->dynamicCspCollector = $dynamicCspCollector;
        $this->policyCollector = $policyCollector;
        $this->cspNonceProvider = $cspNonceProvider;
        $this->htmlPageContent = $htmlPageContent;
        $this->objectManager = $objectManager;
        $this->cacheState = $cacheState;
        $this->appState = $appState ?? ObjectManager::getInstance()->get(AppState::class);
    }

    /**
     * Return the layout instance, lazily instantiating it if it doesn't exist yet.
     *
     * In production mode, instantiating the layout triggers an Area Code not set error in CLI commands.
     * This is triggered, for example, by "bin/magento events:generate:module".
     * Other possible solutions that were ruled out:
     * Because the layout is a widely used object, declaring a Proxy preference in di.xml  is not a good
     * option. Only proxying it for this class constructor is also not an option, since we want the
     * shared state with the regular layout instance.
     */
    private function getLayout(): LayoutInterface
    {
        if (!$this->layout) {
            $this->layout = $this->objectManager->get(LayoutInterface::class);
        }
        return $this->layout;
    }

    public function registerInlineScript(): void
    {
        if (!$this->isAreaCodeSet() || $this->getScriptSrcPolicy()->isInlineAllowed()) {
            return;
        }

        if ($this->cacheState->isEnabled(FullPageCache::TYPE_IDENTIFIER) && $this->getLayout()->isCacheable()) {
            $this->addInlineScriptHashToCspHeader();
        } else {
            $this->addCspNonceToInlineScript();
        }
    }

    private function isAreaCodeSet(): bool
    {
        if ($this->memoizedAreaCode) {
            return true;
        }

        try {
            $this->memoizedAreaCode = $this->appState->getAreaCode();
        } catch (LocalizedException $exception) {
            if ($exception->getMessage() === 'Area code is not set') {
                return false;
            }
        }
        return true;
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

    public function getScriptSrcPolicy(): FetchPolicy
    {
        // Default to policy blocking everything
        return $this->findPolicy('script-src')
            ?? $this->findPolicy('default-src')
            ?? new FetchPolicy('default-src');
    }

    private function findPolicy(string $policyToFind): ?FetchPolicy
    {
        $policies = $this->collectFetchPolicies();
        foreach ($policies as $policy) {
            if ($policy->getId() === $policyToFind) {
                return $policy;
            }
        }

        return null;
    }

    private function collectFetchPolicies(): array
    {
        if (isset($this->memoizedPolicies)) {
            return $this->memoizedPolicies;
        }

        $this->memoizedPolicies = array_filter(
            $this->policyCollector->collect(),
            static function (PolicyInterface $policy) {
                return $policy instanceof FetchPolicy;
            }
        );

        return $this->memoizedPolicies;
    }
}
