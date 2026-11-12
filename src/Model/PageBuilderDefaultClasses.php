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
    private array $contentTypeClasses;

    /**
     * @var string[] Classes keyed by the Page Builder button class they are added to
     */
    private array $buttonClasses;

    public function __construct(array $contentTypeClasses = [], array $buttonClasses = [])
    {
        $this->contentTypeClasses = array_filter($contentTypeClasses);
        $this->buttonClasses = array_filter($buttonClasses);
    }

    public function addToHtml(string $html): string
    {
        if ($this->contentTypeClasses) {
            $html = preg_replace_callback(
                '#<([a-zA-Z][a-zA-Z0-9]*)(\s[^>]*\bdata-content-type="(' . $this->toPattern($this->contentTypeClasses) . ')"[^>]*)>#',
                fn(array $m): string => '<' . $m[1] . $this->mergeClasses($m[2], $this->contentTypeClasses[$m[3]]) . '>',
                $html
            ) ?? $html;
        }

        if ($this->buttonClasses) {
            $html = preg_replace_callback(
                '#<([a-zA-Z][a-zA-Z0-9]*)(\s[^>]*\bclass="[^"]*(?<![\w-])(' . $this->toPattern($this->buttonClasses) . ')(?![\w-])[^"]*"[^>]*)>#',
                fn(array $m): string => '<' . $m[1] . $this->mergeClasses($m[2], $this->buttonClasses[$m[3]]) . '>',
                $html
            ) ?? $html;
        }

        return $html;
    }

    private function toPattern(array $classMap): string
    {
        return implode('|', array_map(fn(string $key): string => preg_quote($key, '#'), array_keys($classMap)));
    }

    private function mergeClasses(string $attributes, string $classes): string
    {
        if (! preg_match('#\sclass="([^"]*)"#', $attributes, $m)) {
            return ' class="' . $classes . '"' . $attributes;
        }

        $merged = array_unique(array_merge(
            preg_split('#\s+#', trim($m[1]), -1, PREG_SPLIT_NO_EMPTY),
            preg_split('#\s+#', trim($classes), -1, PREG_SPLIT_NO_EMPTY)
        ));

        return str_replace($m[0], ' class="' . implode(' ', $merged) . '"', $attributes);
    }
}
