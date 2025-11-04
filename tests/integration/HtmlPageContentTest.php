<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Model\HtmlPageContent;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class HtmlPageContentTest extends TestCase
{
    public function testReturnsEmptyStringIfElementNotPresent(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<div>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('', $sut->extractLastElement($pageContent, 'script'));
    }

    public function testReturnsEmptyStringIfElementNotLast(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<script>console.log()</script>
<div>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('', $sut->extractLastElement($pageContent, 'script'));
    }

    public function testReturnsOuterHTMLIfElementIsLast(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<script>console.log()</script>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('<script>console.log()</script>', $sut->extractLastElement($pageContent, 'script'));
    }

    public function testReturnsOuterHTMLIfElementIsLastWithAttributes(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<script type="text/javascript">console.log()</script>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('<script type="text/javascript">console.log()</script>', $sut->extractLastElement($pageContent, 'script'));
    }

    public function testReturnsOuterHTMLIfElementIsLastWithAttributesWithNewLine(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<script
   type="text/javascript"
   data-test
   nonce="foo"
>
    console.log()
</script>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('<script
   type="text/javascript"
   data-test
   nonce="foo"
>
    console.log()
</script>', $sut->extractLastElement($pageContent, 'script'));
    }

    public function testReturnsInnerTextIfElementIsLastWithAttributesWithNewLine(): void
    {
        $pageContent = <<<EOT
<body>
<div>
Test
<script
   type="text/javascript"
   data-test
   nonce="foo"
>
    console.log()
</script>
EOT;

        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('
    console.log()
', $sut->extractLastElementContent($pageContent, 'script'));
    }

    public function testReturnsEmptyArrayIfNoAttributes(): void
    {
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame([], $sut->getAttributes('<script>'));
    }

    public function testReturnsBooleanAttributesAsTrueValue(): void
    {
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame(['async' => true], $sut->getAttributes('<script async>'));
    }

    public function testReturnsAttributesAsDict(): void
    {
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame(['async' => true, 'foo' => 'bar buz'], $sut->getAttributes('<script async foo="bar buz">'));
        $this->assertSame(['data-foo' => 'bar', 'data-buz' => 'foo'], $sut->getAttributes('<script data-foo="bar" data-buz=\'foo\'>'));
        $this->assertSame(['data-qux' => 'bob', 'data-mux' => 'will'], $sut->getAttributes('<script  data-qux="bob"     data-mux=\'will\'>'));
        $this->assertSame([], $sut->getAttributes('script data-foo="bar" data-buz=\'foo\''));
        $this->assertSame(['foo' => 'bar'], $sut->getAttributes('<div foo=bar>'));
    }

    public function testInjectsStringAttributeValues(): void
    {
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('<div data-foo="bar">', $sut->injectAttribute('<div>', 'data-foo', 'bar'));
        $this->assertSame('<div class="test" data-foo="bar">', $sut->injectAttribute('<div class="test">', 'data-foo', 'bar'));
        $this->assertSame('<div data-foo="bar"/>', $sut->injectAttribute('<div/>', 'data-foo', 'bar'));
        $this->assertSame('<div a="b&quot;" data-foo="bar"/>', $sut->injectAttribute('<div a=\'b"\'/>', 'data-foo', 'bar'));
        $this->assertSame('<input required/>', $sut->injectAttribute('<input/>', 'required'));
        $this->assertSame('<input disabled/>', $sut->injectAttribute('<input/>', 'disabled', true));
        $this->assertSame('<input/>', $sut->injectAttribute('<input/>', 'required', false));
        $this->assertSame('<input/>', $sut->injectAttribute('<input/>', 'required', false));
        $this->assertSame(
            '<script type="text/plain" data-usercentrics="Google Tag Manager" nonce="abcdefg">',
            $sut->injectAttribute('<script type="text/plain" data-usercentrics="Google Tag Manager">', 'nonce', 'abcdefg')
        );
    }

    public function testReturnsTagName(): void
    {
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('div', $sut->getTagName('<div>'));
        $this->assertSame('DIV', $sut->getTagName('<DIV>'));
        $this->assertSame('div', $sut->getTagName('<div required>'));
        $this->assertSame('input', $sut->getTagName('<input/>'));
        $this->assertSame('div', $sut->getTagName('<div data-foo="bar"/>'));
        $this->assertSame('span', $sut->getTagName('<span class="red">'));
        $this->assertSame('', $sut->getTagName('span'));
        $this->assertSame('', $sut->getTagName('< >'));
    }

    public function testReturnsTheFirstTag(): void
    {
        $element = <<<EOT
<script
   type="text/javascript"
   data-test
   nonce="foo"
>
    console.log()
</script>
EOT;
        $sut = ObjectManager::getInstance()->create(HtmlPageContent::class);
        $this->assertSame('<script
   type="text/javascript"
   data-test
   nonce="foo"
>', $sut->getFirstTag($element));

        $this->assertSame('<div class="test">', $sut->getFirstTag('<div class="test"><button type="button">Foo</button></div>'));
        $this->assertSame('<div class="tset">', $sut->getFirstTag('<div class="tset"><button type="button">Bar</button>'));
    }
}
