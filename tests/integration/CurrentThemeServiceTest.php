<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\ObjectManagerInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class CurrentThemeServiceTest extends AbstractController
{
    /** @var CurrentTheme */
    private $themeService;

    protected function setUp(): void
    {
        $this->themeService = Bootstrap::getObjectManager()->get(CurrentTheme::class);
        ThemeFixture::registerTestThemes();
    }

    /** @test */
    public function luma_is_not_hyva()
    {
        $this->givenCurrentTheme('Magento/luma');
        $this->assertFalse($this->themeService->isHyva(), 'Luma should not be recognized as Hyvä theme');
    }

    /** @test */
    public function hyva_default_theme_is_hyva()
    {
        $defaultTheme = ThemeFixture::getInstalledHyvaDefaultThemeCode();
        $this->givenCurrentTheme($defaultTheme);
        $this->assertTrue($this->themeService->isHyva(), 'Hyvä default theme should be recognized as Hyvä theme');
    }

    /** @test */
    public function custom_theme_extending_hyva_default_is_hyva()
    {
        $defaultTheme = ThemeFixture::getInstalledHyvaDefaultThemeCode();
        $this->givenCurrentTheme(substr($defaultTheme, -4) === '-csp' ? 'Custom/extend-csp' : 'Custom/extend');
        $this->assertTrue(
            $this->themeService->isHyva(),
            'Custom theme extending Hyvä default theme should be recognized as Hyvä theme'
        );
    }

    /** @test */
    public function custom_theme_extending_hyva_reset_is_hyva()
    {
        $this->givenCurrentTheme('Custom/copy');
        $this->assertTrue($this->themeService->isHyva(), 'Hyvä test theme should be recognized as Hyvä theme');
    }

    private function givenCurrentTheme(string $themePath): void
    {
        ThemeFixture::setCurrentTheme($themePath);
    }
}
