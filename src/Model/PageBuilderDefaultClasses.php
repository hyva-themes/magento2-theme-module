<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

class PageBuilderDefaultClasses
{
    /**
     * @var string[] Classes keyed by data-content-type value
     */
    private readonly array $contentTypeClasses;

    /**
     * @var string[] Classes keyed by the Page Builder button class they are added to
     */
    private readonly array $buttonClasses;

    public function __construct(array $contentTypeClasses = [], array $buttonClasses = [])
    {
        $this->contentTypeClasses = array_filter($contentTypeClasses);
        $this->buttonClasses = array_filter($buttonClasses);
    }

    public function addToHtml(string $html): string
    {
        $html = $this->addClasses($html, '\bdata-content-type="(%s)"', $this->contentTypeClasses);

        return $this->addClasses($html, '\bclass="[^"]*(?<![\w-])(%s)(?![\w-])[^"]*"', $this->buttonClasses);
    }

    private function addClasses(string $html, string $attributePattern, array $classMap): string
    {
        if (! $classMap) {
            return $html;
        }

        $keys = implode('|', array_map(fn(string $key): string => preg_quote($key, '#'), array_keys($classMap)));

        return preg_replace_callback(
            '#<([a-zA-Z][a-zA-Z0-9]*)(\s[^>]*' . sprintf($attributePattern, $keys) . '[^>]*)>#',
            fn(array $m): string => '<' . $m[1] . $this->mergeClasses($m[2], $classMap[$m[3]]) . '>',
            $html
        ) ?? $html;
    }

    private function mergeClasses(string $attributes, string $classes): string
    {
        $merged = preg_replace_callback(
            '#\sclass="([^"]*)"#',
            fn(array $m): string => ' class="' . implode(' ', array_unique(
                preg_split('#\s+#', $m[1] . ' ' . $classes, -1, PREG_SPLIT_NO_EMPTY)
            )) . '"',
            $attributes,
            1,
            $count
        );

        return $count ? $merged : ' class="' . $classes . '"' . $attributes;
    }
}
