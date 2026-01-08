<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use function array_keys as keys;
use function array_reduce as reduce;
use function array_slice as slice;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged

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
        if (mb_strtolower(mb_substr($trimmedPageContent, - (strlen($tagName) + 3))) !== "</$tagName>") {
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
        $trimmedTag = trim($tag);
        // Prevent errors for very short strings (avoid undefined offset issues)
        if (strlen($trimmedTag) < 2 || $trimmedTag[0] !== '<' || $trimmedTag[strlen($trimmedTag) - 1] !== '>') {
            return [];
        }

        $attributes = [];
        $charStream = $this->toCharStream(trim($trimmedTag, '<> '));

        // move past tag name
        do {
            $c = $charStream->next();
        } while ($c !== '' && ! $charStream->isWhitespaceChar($c));

        // states: out, name, value
        for ($state = 'out', $buffer = '', $currentAttr = '', $quote = ''; $c !== ''; $c = $charStream->next()) {

            if ($state === 'out') {
                if ($charStream->isWhitespaceChar($c)) {
                    continue;
                };

                // start of attribute name
                $buffer .= $c;
                $state = 'name';
                continue;
            } // end out

            if ($state === 'name') {
                if ($charStream->isAtEnd() || $charStream->isWhitespaceChar($c)) {
                    if ($charStream->isAtEnd()) {
                        $buffer .= $c;
                    }

                    if ($charStream->lookAheadToNextNonWhitespaceChar() !== '=') {
                        // end of boolean attribute
                        $attributes[$buffer] = true;
                        $state = 'out';
                        $buffer = '';
                    }
                    continue;
                }

                if ($c === '=') {
                    $state = 'value';
                    $currentAttr = $buffer;
                    // move to start of value
                    do {
                        $c = $charStream->next();
                    } while (! $charStream->isAtEnd() && $charStream->isWhitespaceChar($c));

                    // set buffer to first char of attribute value
                    $buffer = $c;

                    // set type of quote that ends the attribute value
                    if (in_array($c, ['"', "'"], true)) {
                        $quote = $c;
                    }
                    continue;
                }

                // continuation of attribute name
                $buffer .= $c;
                continue;
            } // end name

            if ($state === 'value') {
                if (! $charStream->isWhitespaceChar($c) || $quote) {
                    $buffer .= $c;
                }

                if ($charStream->isAtEnd() || // unquoted attribute value at end of string
                    ($charStream->isWhitespaceChar($c) && ! $quote) || // end of unquoted attr value
                    ($quote && $c === $quote) // end of quoted attribute value
                ) {
                    $attributes[$currentAttr] = $quote
                        ? html_entity_decode(mb_substr($buffer, 1, -1))
                        : $buffer;
                    $currentAttr = '';
                    $state = 'out';
                    $buffer = '';
                    continue;
                }
            }
        } // end value

        return $attributes;
    }

    public function getTagName(string $tag): string
    {
        $trimmedTag = trim($tag);
        // Ensure string length before accessing indices to prevent out-of-bounds errors
        if (strlen($trimmedTag) < 2 || $trimmedTag[0] !== '<' || $trimmedTag[strlen($trimmedTag) - 1] !== '>') {
            return '';
        }
        $parts = preg_split('/\s+/', trim($trimmedTag, '</>'), 2);
        return $parts ? $parts[0] : '';
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
                // Ensure the attribute value is safely encoded to prevent XSS vulnerabilities.
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
                $acc[] = sprintf('%s="%s"', $attributeName, htmlspecialchars($value, ENT_QUOTES | ENT_HTML5));
            }
            return $acc;
        }, [$this->getTagName($tag)]));

        return '<' . $tagData . ($this->isSelfClosing($tag) ? '/' : '') .  '>';
    }

    public function getFirstTag(string $element): string
    {
        if ($element === '' || $element[0] !== '<') {
            return '';
        }
        return substr($element, 0, strpos($element, '>') + 1);
    }

    private function toCharStream(string $str): object
    {
        return new class($str) {
            private $s = '';
            private $i = 0;

            public function __construct(string $str)
            {
                $this->s = $str;
            }

            public function current(): string
            {
                return ! $this->isAtEnd()
                    ? mb_substr($this->s, $this->i, 1)
                    : '';
            }

            public function next(): string
            {
                $c = $this->current();
                $this->i++;

                return $c;
            }

            public function isAtEnd(): bool
            {
                return $this->i >= mb_strlen($this->s);
            }

            public function isWhitespaceChar(string $c): bool
            {
                return in_array($c, [" ", "\n", "\t"], true);
            }

            public function lookAheadToNextNonWhitespaceChar(): string
            {
                $i = $this->i;
                do {
                    $c = $this->next();
                } while ($c !== '' && ! $this->isWhitespaceChar($c));

                $this->i = $i;

                return $c;
            }
        };
    }
}
