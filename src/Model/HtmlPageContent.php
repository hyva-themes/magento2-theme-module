<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use function array_keys as keys;
use function array_reduce as reduce;
use function array_slice as slice;


class HtmlPageContent
{
    /**
     * Extract tag outerHTML from the partial page DOM, if it is the last DOM element in the page content.
     *
     * We don't use a regex because of expensive backtracking.
     *
     * We don't use DOMDocument because $pageContent may be a partial DOM tree consisting
     * only of the nodes up to the script tag, that is, many elements are unclosed.
     *
     * Returns the tag outerHtml or an empty string if it isn't the last element on the page.
     */
    public function extractLastElement(string $pageContent, string $tagName): string
    {
        $trimmedPageContent = rtrim($pageContent);
        $tagName = mb_strtolower($tagName);
        if (mb_strtolower(mb_substr($trimmedPageContent, -9)) !== "</$tagName>") {
            return '';
        }
        // Find <tag> or <tag data-foo="bar"> or possibly other attributes
        $startPos = mb_strripos($trimmedPageContent, "<$tagName");
        if ($startPos === false) {
            return '';
        }
        return mb_substr($trimmedPageContent, $startPos);
    }

    /**
     * Extract tag innerText from the partial page DOM, if it is the last DOM element in the page content.
     *
     * We don't use a regex because of expensive backtracking.
     */
    public function extractLastElementContent(string $pageContent, string $tagName): string
    {
        $element = $this->extractLastElement($pageContent, $tagName);
        if (! $element) {
            return '';
        }

        $endOfStartTagPos = mb_strpos($element, '>');
        $startOfEndTagPos = mb_strrpos($element, '<');
        return mb_substr($element, $endOfStartTagPos + 1, (mb_strlen($element) - $startOfEndTagPos) * -1);
    }

    /**
     * Return array of attributes from given tag as key/value pairs, quotes removed if present.
     *
     * Boolean attributes are returned as key => true.
     *
     * @param string $tag
     * @return mixed[]
     */
    public function getAttributes(string $tag): array
    {
        $trimmedTag = mb_trim($tag);
        if ($trimmedTag[0] !== '<' || $tag[strlen($trimmedTag) -1] !== '>') {
            return [];
        }
        $parts = slice(preg_split('/\s+/', trim($trimmedTag, '<>'), -1, PREG_SPLIT_NO_EMPTY), 1);
        return reduce($parts, function (array $acc, string $part): array {
            if (strpos($part, '=')) {
                $key = substr($part, 0, strpos($part, '='));
                $value = substr($part, strpos($part, '=') + 1);
                if (in_array($value[0], ['"', "'"], true) && mb_substr($value, -1) === $value[0]) {
                    $value = stripslashes(mb_substr($value, 1, -1));
                }
                $acc[$key] = $value;
            } else {
                $acc[$part] = true;
            }
            return $acc;
        }, []);
    }

    public function getTagName(string $tag): string
    {
        $trimmedTag = mb_trim($tag);
        if ($trimmedTag[0] !== '<' || $tag[strlen($trimmedTag) -1] !== '>') {
            return '';
        }

        return preg_split('/\s+/', trim($trimmedTag, '</>'), 2)[0];
    }

    private function isSelfClosing(string $tag): bool
    {
        return substr(rtrim($tag), -2, 1) === '/';
    }

    public function injectAttribute(string $tag, string $attributeName, $attributeValue = true): string
    {
        $attributes = $this->getAttributes($tag);
        $attributes[strtolower($attributeName)] = $attributeValue;

        $tagData = implode(' ', reduce(keys($attributes), function (array $acc, string $attributeName) use ($attributes) {
            $value = $attributes[$attributeName];
            if ($value === true) {
                $acc[] = $attributeName;
            } elseif ($value !== false) {
                $acc[] = sprintf('%s="%s"', $attributeName, addslashes($value));
            }
            return $acc;
        }, [$this->getTagName($tag)]));

        return '<' . $tagData . ($this->isSelfClosing($tag) ? '/' : '') .  '>';
    }

    public function getFirstTag(string $element): string
    {
        if ($element[0] !== '<') {
            return '';
        }
        return substr($element, 0, strpos($element, '>') + 1);
    }
}
