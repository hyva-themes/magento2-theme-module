<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\Escaper\EscapeHtmlAllowingAnchorAttributes;
use PHPUnit\Framework\TestCase;

// phpcs:disable Generic.Files.LineLength.TooLong

/**
 * @covers \Hyva\Theme\ViewModel\Escaper\EscapeHtmlAllowingAnchorAttributes
 */
class EscapeHtmlAllowingAnchorAttributesTest extends TestCase
{
    /**
     * @dataProvider escapeHtmlExamples
     */
    public function testEscapeHtml(string $input, string $expected): void
    {
        $escapeAllowingAnchor = new EscapeHtmlAllowingAnchorAttributes();

        $this->assertSame($expected, $escapeAllowingAnchor->escapeHtml($input, ['a']));
    }

    public function escapeHtmlExamples(): array
    {
        return [
            'allow-anchor' => [
                'input' => 'This form is protected by reCAPTCHA - the <a class="underline" href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a> and <a class="underline" href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a> apply.',
                'expect' => 'This form is protected by reCAPTCHA - the <a class="underline" href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</a> and <a class="underline" href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</a> apply.'
            ],
            'filter-div' => [
                'input' => 'This form is protected by reCAPTCHA - the <div class="underline" href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google Privacy Policy</div> and <div class="underline" href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms of Service</div> apply.',
                'expect' => 'This form is protected by reCAPTCHA - the Google Privacy Policy and Terms of Service apply.'
            ],
            'filter-dummy-attrs' => [
                'input' => 'This link is <a href="#" foo="bar">very safe</a> - trust us!',
                'expect' => 'This link is <a href="#">very safe</a> - trust us!',
            ]
        ];
    }
}
