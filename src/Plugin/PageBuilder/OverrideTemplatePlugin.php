<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\PageBuilder;

use Hyva\Theme\Service\CurrentTheme;
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
     * @param CurrentTheme $theme
     * @param ConfigInterface $viewConfig
     */
    public function __construct(CurrentTheme $theme, MathRandom $mathRandom)
    {
        $this->theme = $theme;
        $this->mathRandom = $mathRandom;
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
        }

        $result = $proceed($interceptor, $result);

        if ($this->theme->isHyva() && is_string($result)) {
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
        while (preg_match('/<[a-zA-Z][^>]+?\s(@[^=]+)=/', $content, $matches)) {
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
}
