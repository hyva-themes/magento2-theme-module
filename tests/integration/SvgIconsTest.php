<?php
declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Service\CurrentTheme;
use Hyva\Theme\ViewModel\Heroicons;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 */
class SvgIconsTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private ?array $testViewFiles;

    protected function setUp(): void
    {
        $this->testViewFiles = [];
        $this->objectManager = Bootstrap::getObjectManager();
        ThemeFixture::registerTestThemes();
    }

    protected function tearDown(): void
    {
        foreach ($this->testViewFiles as $testViewFile) {
            \unlink($testViewFile);
        }
    }

    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_code(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml($code)));
    }

    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_magic_method(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons|Heroicons $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\SvgIcons::class);
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
        $this->givenCurrentTheme('Hyva/test');
        $overriddenSvg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="5" d="M5 13l4 4L19 7"/>
            </svg>
            SVG;
        $this->createViewFile('Hyva_Theme/web/svg/heroicons/outline/check.svg', $overriddenSvg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons|Heroicons $svgIcons */
        $svgIcons = $this->objectManager->get(\Hyva\Theme\ViewModel\SvgIcons::class);
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
        $this->givenCurrentTheme('Hyva/test');
        $svg = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="10" d="M5 13l4 4L19 7"/>
            </svg>
            SVG;
        $this->createViewFile('Hyva_Theme/web/svg/custom/custom-icon.svg', $svg);
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = $this->objectManager->create(\Hyva\Theme\ViewModel\SvgIcons::class, ['iconSet' => 'custom']);
        $this->assertEquals(
            $svg,
            trim($svgIcons->renderHtml('custom-icon'))
        );
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
        $viewFilePath = __DIR__ . '/_files/design/frontend/Hyva/test/' . $viewFile . '';
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
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                SVG,
            ],
            'arrow-up' => [
                'arrow-up',
                'arrowUpHtml',
                <<<'SVG'
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
                SVG,
            ],
        ];
    }
}
