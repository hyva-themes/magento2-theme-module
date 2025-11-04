<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin;

use Hyva\Theme\ThemeFixture;
use Magento\Framework\Filter\Template as TemplateFilter;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Hyva\Theme\Plugin\PageBuilder\OverrideTemplatePlugin
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 */
class OverrideTemplatePluginTest extends TestCase
{
    protected function setUp(): void
    {
        ThemeFixture::registerTestThemes();
        ThemeFixture::setHyvaDefaultAsCurrentTheme();
    }

    private function executeSutWithContent(string $content): string
    {
        return ObjectManager::getInstance()->get(TemplateFilter::class)->filter($content);
    }

    public function testEmptyStringInputHasNoEffect(): void
    {
        $this->assertSame('', $this->executeSutWithContent(''));
    }

    public function testReplacesAlpineAttributes(): void
    {
        $content = <<<EOT
<div data-content-type="html" data-decoded="true">
<button @click="foo++">Example</button><span>Some content with an @click</span>
</div>
EOT;
        $expected = <<<EOT
<div data-content-type="html">
<button @click="foo++">Example</button><span>Some content with an @click</span>
</div>
EOT;

        $this->assertSame($content, $this->executeSutWithContent($content));
    }

    public function testDoesNotMaskTailwindContainerQueryClasses(): void
    {
        $content = <<<EOT
<div data-content-type="html">
<button
    class="@container/add w-full flex gap-3 text"
    title="Add to Cart">
    <!-- Mobile -->
    <span class="inline uppercase @[180px]/add:hidden">Add to bag</span>
    <!-- Desktop -->
    <span class="hidden uppercase @[180px]/add:inline">Add to bag</span>
</button>
</div>
EOT;
        $expected = <<<EOT
<div data-content-type="html" data-decoded="true">
<button class="@container/add w-full flex gap-3 text" title="Add to Cart">
    <!-- Mobile -->
    <span class="inline uppercase @[180px]/add:hidden">Add to bag</span>
    <!-- Desktop -->
    <span class="hidden uppercase @[180px]/add:inline">Add to bag</span>
</button>
</div>
EOT;

        $this->assertSame($expected, $this->executeSutWithContent($content));
    }

    public function testDoesNotReplaceTailwindContainerQueryClassesButReplacesAlpineAttributes(): void
    {
        $content = <<<EOT
<div data-content-type="html">
<button
    @click="foo"
    class="@container/add w-full flex gap-3 text"
    title="Add to Cart">
    <!-- Mobile -->
    <span class="inline uppercase @[180px]/add:hidden" @test="foo">Add to bag</span>
    <!-- Desktop -->
    <span @test="foo" class="hidden uppercase @[180px]/add:inline">Add to bag</span>
</button>
</div>
EOT;
        $expected = <<<EOT
<div data-content-type="html" data-decoded="true">
<button @click="foo" class="@container/add w-full flex gap-3 text" title="Add to Cart">
    <!-- Mobile -->
    <span class="inline uppercase @[180px]/add:hidden" @test="foo">Add to bag</span>
    <!-- Desktop -->
    <span @test="foo" class="hidden uppercase @[180px]/add:inline">Add to bag</span>
</button>
</div>
EOT;

        $this->assertSame($expected, $this->executeSutWithContent($content));
    }
}
