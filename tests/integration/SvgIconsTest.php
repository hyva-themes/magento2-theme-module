<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\SvgIcons;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps, Generic.Files.LineLength.TooLong

/**
 * @covers \Hyva\Theme\ViewModel\SvgIcons
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SvgIconsTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[]|null */
    private $testViewFiles = [];

    protected function setUp(): void
    {
        $this->testViewFiles = [];
        $this->objectManager = Bootstrap::getObjectManager();
        /** @var CacheInterface $cache */
        $cache = $this->objectManager->get(CacheInterface::class);
        $cache->clean([SvgIcons::CACHE_TAG]);
        ThemeFixture::registerTestThemes();

        $reflectedClass = new \ReflectionClass(SvgIcons::class);
        $prop = $reflectedClass->getProperty('internalIdUsageCounts');
        $prop->setAccessible(true);
        $prop->setValue($reflectedClass, []);
        $prop->setAccessible(false);
    }

    protected function tearDown(): void
    {
        foreach ($this->testViewFiles as $testViewFile) {
            \unlink($testViewFile);
        }
    }

    private function givenCurrentTheme(string $themePath): void
    {
        /** @var Registration $registration */
        $registration = $this->objectManager->get(Registration::class);
        $registration->register();

        /** @var DesignInterface $design */
        $design = $this->objectManager->get(DesignInterface::class);
        $design->setDesignTheme($themePath);
    }

    private function createViewFile(string $viewFile, string $viewFileContents): void
    {
        $viewFilePath = __DIR__ . '/_files/design/frontend/Hyva/integration-test/' . $viewFile . '';
        \file_put_contents(
            $viewFilePath,
            $viewFileContents
        );
        $this->testViewFiles[] = $viewFilePath;
    }

    public function dataSvg()
    {
        return [
            'check'    => [
                'check',
                'checkHtml',
                <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24" role="img">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
<title>check</title></svg>
SVG,
            ],
            'arrow-up' => [
                'arrow-up',
                'arrowUpHtml',
                <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="24" height="24" role="img">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
<title>arrow-up</title></svg>
SVG,
            ],
        ];
    }

    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_code(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml($code)));
    }

    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_magic_method(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $this->assertEquals(
            $expectedSvg,
            trim($svgIcons->$method())
        );
    }

    /**
     * @test
     */
    public function svg_can_be_overridden_in_theme()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $overriddenSvg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" role="img">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M5 13l4 4L19 7"/>
            <title>check</title></svg>
            SVG;
        $this->createViewFile('Hyva_Theme/web/svg/heroicons/outline/check.svg', $overriddenSvg);
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $this->assertEquals(
            $overriddenSvg,
            trim($svgIcons->checkHtml())
        );
    }

    /**
     * @test
     */
    public function can_use_arbitrary_icon_set_in_theme()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $svg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" role="img">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            <title>custom-icon</title></svg>
            SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom/custom-icon.svg', $svg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconSet' => 'custom']);
        $this->assertEquals(
            $svg,
            trim($svgIcons->renderHtml('custom-icon'))
        );
    }

    /**
     * @test
     */
    public function can_be_used_without_icon_set_in_theme()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $svg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="24" height="24" role="img">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            <title>custom-icon</title></svg>
            SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $svg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertEquals(
            $svg,
            trim($svgIcons->renderHtml('custom-icon'))
        );
        $this->assertEquals(
            $svg,
            trim($svgIcons->customIconHtml())
        );
    }

    /**
     * @test
     */
    public function adds_css_classes()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6" width="24" height="24" role="img">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
<title>check</title></svg>
SVG;
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('check', 'h-6 w-6')));
    }

    /**
     * @test
     */
    public function adds_width_and_height()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="12" role="img">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
<title>check</title></svg>
SVG;
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('check', '', 16, 12)));
    }

    /**
     * @test
     */
    public function adds_role_attribute()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>check</title></svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" class="h-5 w-5" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>check</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $inputSvg);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('custom-icon', 'h-5 w-5', 12, 12)));
    }

    /**
     * @test
     */
    public function can_process_boolean_attribute_values()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" role="img" foo="true" bar="true" baz="false" qux="false"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>custom-icon</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $inputSvg);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('custom-icon', '', 12, 12, [
            'foo' => true,
            'bar' => 'true',
            'baz' => false,
            'qux' => 'false'
        ])));
    }

    /**
     * @test
     */
    public function adds_title_node()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>custom-icon</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $inputSvg);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('custom-icon', '', 12, 12)));
    }

    /**
     * @test
     */
    public function adds_custom_title_node()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" role="img" foo="bar"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>Example</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $inputSvg);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('custom-icon', '', 12, 12, ['title' => 'Example', 'foo' => 'bar'])));
    }

    /**
     * @test
     */
    public function can_process_titles_with_umlauts()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/></svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12" role="img"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 13 4 4L19 7"/><title>Foo &amp; Bar - &#xC4;hm</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $inputSvg);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml('custom-icon', '', 12, 12, ['title' => 'Foo & Bar - Ähm'])));
    }

    /**
     * @test
     */
    public function replaces_existing_attributes()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="" width="500" height="500">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
</svg>
SVG;
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-5 w-5" width="12" height="12" role="img">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
<title>custom-icon</title></svg>
SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom-icon.svg', $svg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertEquals(
            $expectedSvg,
            trim($svgIcons->renderHtml('custom-icon', 'h-5 w-5', 12, 12))
        );
    }

    /**
     * @test
     */
    public function adds_classes_width_and_height_with_magic_method()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $expectedSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="text-red" width="16" height="12" role="img">
  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
<title>check</title></svg>
SVG;
        $this->assertEquals($expectedSvg, trim($svgIcons->checkHtml('text-red', 16, 12)));
    }

    /**
     * @test
     */
    public function renders_repeated_icon_fast()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $startTime = microtime(true);
        for ($i=0; $i<100; ++$i) {
            $svgIcons->renderHtml('clock');
        }
        $seconds = microtime(true) - $startTime;
        $this->assertLessThan(0.05, $seconds, 'Rendering the same SVG 100 times should take less than 50ms');
    }

    /**
     * @test
     */
    public function caches_icons_based_on_width_and_height()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $first = $svgIcons->renderHtml('cake', 'w-6 h-6', 32, 32);
        $second = $svgIcons->renderHtml('cake', 'w-6 h-6', 16, 16);
        $this->assertNotEquals($first, $second, 'Different width + height parameters should result in different SVGs');
    }

    /**
     * @test
     */
    public function caches_icons_based_on_attributes()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $first = $svgIcons->renderHtml('cake', 'w-6 h-6', 16, 16, ['title' => 'Test A']);
        $second = $svgIcons->renderHtml('cake', 'w-6 h-6', 16, 16, ['title' => 'Test B']);
        $this->assertNotEquals($first, $second, 'Different attribute parameters should result in different SVGs');
    }

    /**
     * @test
     */
    public function caches_icons_based_on_class_names()
    {
        /** @var \Hyva\Theme\ViewModel\HeroiconsOutline $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\HeroiconsOutline::class);
        $first = $svgIcons->renderHtml('document', 'w-6 h-6');
        $second = $svgIcons->renderHtml('document', 'w-5 h-5');
        $this->assertNotEquals($first, $second, 'Different class names should result in different SVGs');
    }

    /**
     * @test
     */
    public function caches_icons_based_on_icon_set()
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $outlineIcons */
        /** @var \Hyva\Theme\ViewModel\SvgIcons $solidIcons */
        $outlineIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconSet' => 'heroicons/outline']);
        $solidIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconSet' => 'heroicons/solid']);
        $first = $outlineIcons->renderHtml('eye', 'w-6 h-6');
        $second = $solidIcons->renderHtml('eye', 'w-6 h-6');
        $this->assertNotEquals($first, $second, 'Different icon sets for the same icon should result in different SVGs');
    }

    /**
     * @test
     */
    public function caches_icons_based_on_icon_path_prefix()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $this->createViewFile('Hyva_PaymentIcons/web/svg/dark/eye.svg', <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="" width="500" height="500">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            </svg>
            SVG
        );
        $iconsA = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconPathPrefix' => 'Hyva_Theme::svg/heroicons/outline']);
        $iconsB = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconPathPrefix' => 'Hyva_PaymentIcons::svg/dark']);
        $first = $iconsA->renderHtml('eye', 'w-6 h-6');
        $second = $iconsB->renderHtml('eye', 'w-6 h-6');
        $this->assertNotEquals($first, $second, 'Different iconPathPrefix for the same icon should result in different SVGs');
    }

    /**
     * @test
     */
    public function applies_icon_path_prefix_di_config()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $idealSvg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="blue" class="" width="24" height="24" role="img">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            <title>payment-icons/dark/ideal</title></svg>
            SVG;
        $cartSvg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="black" class="" width="24" height="24" role="img">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            <title>heroicons/solid/shopping-cart</title></svg>
            SVG;
        $this->createViewFile('Hyva_PaymentIcons/web/svg/dark/ideal.svg', $idealSvg);
        $this->createViewFile('web/svg/cart.svg', $cartSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, [
            'pathPrefixMapping' => [
                'payment-icons' => 'Hyva_PaymentIcons::svg',
            ],
        ]);

        $this->assertNotEmpty($icons->renderHtml('heroicons/solid/shopping-cart'));
        $this->assertSame($idealSvg, trim($icons->renderHtml('payment-icons/dark/ideal')));
        $this->assertSame($cartSvg, trim($icons->renderHtml('cart')));
    }

    /**
     * @test
     */
    public function throws_beginner_friendly_error()
    {
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Unable to find the SVG icon "non-existent-icon');
        $icons->renderHtml('non-existent-icon');
    }

    /**
     * @test
     */
    public function does_not_disambiguate_single_use_internal_ids()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $testSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 295 59" role="img">
    <defs>
        <linearGradient id="aaa" x1="24.14" y1="57.36" x2="37.43" y2="50.44" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#2c4d9d"/>
            <stop offset="1" stop-color="#3a8fce"/>
        </linearGradient>
        <linearGradient id="bbb" x1="17.63" y1="51.23" x2="56.29" y2="11.21" xlink:href="#aaa"/>
    </defs>
    <path d="M37.28,50.68a1.64," style="fill:url(#aaa)"/>
    <path d="M71.45,29.54a6.63," style="fill:url(#bbb)"/>
<title>test</title></svg>
SVG;
        $this->createViewFile('web/svg/test.svg', $testSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertSame($testSvg, trim($icons->renderHtml('test')));
    }

    /**
     * @test
     */
    public function disambiguate_internal_ids_over_multiple_instances_of_the_same_icon()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 295 59" role="img">
    <defs>
        <linearGradient id="aaa" x1="24.14" y1="57.36" x2="37.43" y2="50.44" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#2c4d9d"/>
            <stop offset="1" stop-color="#3a8fce"/>
        </linearGradient>
        <linearGradient id="bbb" x1="17.63" y1="51.23" x2="56.29" y2="11.21" xlink:href="#aaa"/>
    </defs>
    <path d="M37.28,50.68a1.64," style="fill:url(#aaa)"/>
    <path d="M71.45,29.54a6.63," style="fill:url(#bbb)"/>
<title>test</title></svg>
SVG;
        $expectedSvg1 = $inputSvg;
        $expectedSvg2 = str_replace(['aaa', 'bbb'], ['aaa_2', 'bbb_2'], $inputSvg);
        $this->createViewFile('web/svg/test.svg', $inputSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertSame($expectedSvg1, trim($icons->renderHtml('test')));
        $this->assertSame($expectedSvg2, trim($icons->renderHtml('test')));
    }

    /**
     * @test
     */
    public function disambiguate_internal_ids_over_instances_of_different_same_icons()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 295 59" role="img">
    <defs>
        <linearGradient id="aaa" x1="24.14" y1="57.36" x2="37.43" y2="50.44" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#2c4d9d"/>
            <stop offset="1" stop-color="#3a8fce"/>
        </linearGradient>
        <linearGradient id="bbb" x1="17.63" y1="51.23" x2="56.29" y2="11.21" xlink:href="#aaa"/>
    </defs>
    <path d="M37.28,50.68a1.64," style="fill:url(#aaa)"/>
    <path d="M71.45,29.54a6.63," style="fill:url(#bbb)"/>
<title>test1</title></svg>
SVG;
        $expectedSvg1 = $inputSvg;
        $expectedSvg2 = str_replace(['aaa', 'bbb'], ['aaa_2', 'bbb_2'], $inputSvg);

        $this->createViewFile('web/svg/test1.svg', $inputSvg);
        $this->createViewFile('web/svg/test2.svg', $inputSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertSame($expectedSvg1, trim($icons->renderHtml('test1')));
        $this->assertSame($expectedSvg2, trim($icons->renderHtml('test2')));
    }

    /**
     * @test
     */
    public function does_not_disambiguate_hex_colors_with_id_overlap()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 295 59" role="img">
    <defs>
        <linearGradient id="aa" x1="24.14" y1="57.36" x2="37.43" y2="50.44" gradientUnits="userSpaceOnUse">
            <stop offset="0" stop-color="#aabbcc"/>
            <stop offset="1" stop-color="#ccbbaa"/>
        </linearGradient>
        <linearGradient id="cc" x1="17.63" y1="51.23" x2="56.29" y2="11.21" xlink:href="#aaa"/>
    </defs>
    <path d="M37.28,50.68a1.64," style="fill:url(#aa)"/>
    <path d="M71.45,29.54a6.63," style="fill:url(#cc)"/>
<title>test</title></svg>
SVG;
        $expectedSvg1 = $inputSvg;
        $expectedSvg2 = str_replace(['id="aa"', 'id="cc"', 'url(#aa)', 'url(#cc)'], ['id="aa_2"', 'id="cc_2"', 'url(#aa_2)', 'url(#cc_2)'], $inputSvg);
        $this->createViewFile('web/svg/test.svg', $inputSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertSame($expectedSvg1, trim($icons->renderHtml('test')));
        $this->assertSame($expectedSvg2, trim($icons->renderHtml('test')));
    }

    /**
     * @test
     */
    public function can_process_alpine_attributes()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $inputSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" :class="{'hidden': true}" @click.window="open=true" width="500" height="500" role="img">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
<title>test</title></svg>
SVG;
        $this->createViewFile('web/svg/test.svg', $inputSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $icons */
        $icons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertSame($inputSvg, trim($icons->renderHtml('test', '', null, null)));
    }
}
