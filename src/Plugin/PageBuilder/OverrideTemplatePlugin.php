<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageBuilder;

use Hyva\Theme\Model\InjectImageDimensions;
use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filter\Template as FrameworkTemplateFilter;
use Magento\Framework\Math\Random as MathRandom;
use Magento\Framework\View\ConfigInterface;
use Magento\PageBuilder\Plugin\Filter\TemplatePlugin;

class OverrideTemplatePlugin
{
    /**
     * @var CurrentTheme
     */
    protected $theme;

    /**
     * @var MathRandom
     */
    private $mathRandom;

    /**
     * @var string[]
     */
    private $maskedAttributes = [];

    /**
     * @var InjectImageDimensions
     */
    private $injectImageDimensions;

    /**
     * @param CurrentTheme $theme
     * @param ConfigInterface $viewConfig
     */
    public function __construct(
        CurrentTheme $theme,
        MathRandom $mathRandom,
        ?InjectImageDimensions $injectImageDimensions = null
    ) {
        $this->theme = $theme;
        $this->mathRandom = $mathRandom;
        $this->injectImageDimensions = $injectImageDimensions ?? ObjectManager::getInstance()->get(InjectImageDimensions::class);
    }

    /**
     * On Hyvä frontends, replace this plugin to prevent attributes like `@resize.window=""` from being removed.
     *
     * @param TemplatePlugin $subject
     * @param \Closure $proceed
     * @param FrameworkTemplateFilter $interceptor
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundAfterFilter(
        TemplatePlugin $subject,
        \Closure $proceed,
        FrameworkTemplateFilter $interceptor,
        $result
    ): string {
        if ($this->theme->isHyva() && is_string($result)) {
            $result = $this->maskAlpineAttributes($result);
            $result = $this->injectImageDimensions->addNativeImageDimensionsToPageBuilderImages($result);
        }

        $result = $proceed($interceptor, $result);

        if ($this->theme->isHyva() && is_string($result)) {
            $result = $this->removeEagerLoadingBackgroundImageStyles($result);
            $result = $this->unmaskAlpineAttributes($result);
        }
        return $result;
    }

    private function generateMaskString(): string
    {
        do {
            $mask = $this->mathRandom->getRandomString(32, $this->mathRandom::CHARS_LOWERS);
        } while (isset($this->maskedAttributes[$mask]));

        return $mask;
    }

    private function maskAlpineAttributes(string $content): string
    {
        while (preg_match('/<[a-zA-Z][^>]+?\s(@[^=>"\']+)=/', $content, $matches)) {
            $mask = $this->generateMaskString();
            $this->maskedAttributes[$mask] = $matches[1];
            $content = str_replace($matches[1], $mask, $content);
        }

        return $content;
    }

    private function unmaskAlpineAttributes(string $content): string
    {
        return str_replace(array_keys($this->maskedAttributes), array_values($this->maskedAttributes), $content);
    }

    /**
     * Remove the lazy loaded CSS generated in \Magento\PageBuilder\Model\Filter\Template::generateBackgroundImageStyles.
     *
     * They will be set as the background image url by frontend code.
     */
    private function removeEagerLoadingBackgroundImageStyles(string $result): string
    {
        $backgroundIds = [];
        // Match all lazy loading background image elements
        if (preg_match_all('/(<[^>]+data-background-lazy-load="true"[^>]+>)/s', $result, $matches)) {
            $bgElements = $matches[1];
            // Capture all background image ids
            if (preg_match_all('/class="[^"]*background-image-([a-z0-9]+)/', implode('', $bgElements), $matches)) {
                $backgroundIds = array_merge($backgroundIds, $matches[1] ?? []);
            }
        }

        if ($backgroundIds) {
            $idsGroup = implode('|', array_map('preg_quote', $backgroundIds));
            $regex = sprintf('#<style type="text/css">(?:@media only screen and [^<]+)?\.background-image-(?:%s).+?</style>#s', $idsGroup);
            return preg_replace($regex, '', $result);
        }

        return $result;
    }
}
