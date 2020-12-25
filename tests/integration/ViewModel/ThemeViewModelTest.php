<?php
declare(strict_types=1);

namespace integration\ViewModel;

use Hyva\Theme\ViewModel\Theme;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\Theme\Model\Theme\Registration;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 */
class ThemeViewModelTest extends AbstractController
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var Theme */
    private $themeViewModel;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->themeViewModel = Bootstrap::getObjectManager()->get(Theme::class);
        $this->registerTestThemes();
    }

    /** @test */
    public function luma_is_not_hyva()
    {
        $this->givenCurrentTheme('Magento/luma');
        $this->assertFalse($this->themeViewModel->isHyva(), 'Luma should not be recognized as Hyvä theme');
    }

    /** @test */
    public function hyva_default_theme_is_hyva()
    {
        $this->givenCurrentTheme('Hyva/default');
        $this->assertTrue($this->themeViewModel->isHyva(), 'Hyvä default theme should be recognized as Hyvä theme');
    }

    /** @test */
    public function custom_theme_extending_hyva_default_is_hyva()
    {
        $this->givenCurrentTheme('Custom/extend');
        $this->assertTrue(
            $this->themeViewModel->isHyva(),
            'Custom theme extending Hyvä default theme should be recognized as Hyvä theme'
        );
    }

    /** @test */
    public function custom_theme_extending_hyva_reset_is_hyva()
    {
        $this->givenCurrentTheme('Custom/copy');
        $this->assertTrue($this->themeViewModel->isHyva(), 'Hyvä test theme should be recognized as Hyvä theme');
    }

    /**
     * Re-register themes from the magentoComponentsDir fixture
     */
    private function registerTestThemes(): void
    {
        /** @var Registration $registration */
        $registration = $this->objectManager->get(Registration::class);
        $registration->register();
    }

    private function givenCurrentTheme(string $themePath): void
    {
        /** @var DesignInterface $design */
        $design = Bootstrap::getObjectManager()->get(DesignInterface::class);
        $design->setDesignTheme($themePath);
    }
}
