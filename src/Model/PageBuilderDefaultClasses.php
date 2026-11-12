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
     * @var string[] Classes keyed by the data-content-type value of the elements they are added to
     */
    private readonly array $classesByContentType;

    /**
     * @var string[] Classes keyed by the CSS class of the elements they are added to
     */
    private readonly array $classesByCssClass;

    public function __construct(array $classesByContentType = [], array $classesByCssClass = [])
    {
        $this->classesByContentType = array_filter($classesByContentType);
        $this->classesByCssClass = array_filter($classesByCssClass);
    }

    public function addToHtml(string $html): string
    {
        $html = $this->addClasses($html, '\bdata-content-type="(%s)"', $this->classesByContentType);

        return $this->addClasses($html, '\bclass="[^"]*(?<![\w-])(%s)(?![\w-])[^"]*"', $this->classesByCssClass);
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
