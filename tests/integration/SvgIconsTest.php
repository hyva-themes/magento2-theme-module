<?php
declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\Heroicons;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class SvgIconsTest extends TestCase
{
    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_code(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons $svgIcons */
        $svgIcons = Bootstrap::getObjectManager()->get(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertEquals($expectedSvg, trim($svgIcons->renderHtml($code)));
    }

    /**
     * @test
     * @dataProvider dataSvg
     */
    public function renders_svg_with_magic_method(string $code, string $method, string $expectedSvg)
    {
        /** @var \Hyva\Theme\ViewModel\SvgIcons|Heroicons $svgIcons */
        $svgIcons = Bootstrap::getObjectManager()->get(\Hyva\Theme\ViewModel\SvgIcons::class);
        $this->assertEquals(
            $expectedSvg,
            trim($svgIcons->$method())
        );
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
