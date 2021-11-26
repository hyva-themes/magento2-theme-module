<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;
use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class TemplateVariableTest extends TestCase
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var PageFactory */
    private $pageFactory;

    /** @var string[] */
    private ?array $testTemplates;

    protected function setUp(): void
    {
        $this->testTemplates = [];
        $this->objectManager = Bootstrap::getObjectManager();
        $this->pageFactory = $this->objectManager->get(PageFactory::class);
        $this->registerTestTheme();
    }

    protected function tearDown(): void
    {
        foreach ($this->testTemplates as $template) {
            \unlink($template);
        }
    }

    /**
     * @test
     */
    public function view_model_container_is_available_as_template_variable()
    {
        $this->createTemplate(
            'Hyva_Theme/templates/test.phtml',
            <<<'PHTML'
            <?php
            \PHPUnit\Framework\Assert::assertTrue(isset($viewModels), '$viewModels variable should be set in template');
            \PHPUnit\Framework\Assert::assertInstanceOf(
                \Hyva\Theme\Model\ViewModelRegistry::class,
                $viewModels,
                '$viewModels variable should be instance of ViewModelRegistry'
            );
            echo "RENDERED";
            PHTML
        );
        $block = $this->createBlockWithTemplate('Hyva_Theme::test.phtml');
        $this->assertEquals(
            'RENDERED',
            $block->toHtml(),
            "The test block should be rendered\n\nThis is to make sure the assertions in the template were executed"
        );
    }

    /**
     * @test
     */
    public function global_view_models_are_accessible_through_container_in_template()
    {
        $this->createTemplate(
            'Hyva_Theme/templates/test.phtml',
            <<<'PHTML'
            <?php
            \PHPUnit\Framework\Assert::assertTrue(isset($viewModels), '$viewModels variable should be set in template');
            \PHPUnit\Framework\Assert::assertInstanceOf(
                \Hyva\Theme\Model\ViewModelRegistry::class,
                $viewModels,
                '$viewModels variable should be instance of ViewModelRegistry'
            );
            \PHPUnit\Framework\Assert::assertInstanceOf(
                \Hyva\Theme\ViewModel\StoreConfig::class,
                $viewModels->require(\Hyva\Theme\ViewModel\StoreConfig::class),
                'StoreConfig view model should be found in view model registry'
            );
            echo "RENDERED";
            PHTML
        );
        $block = $this->createBlockWithTemplate('Hyva_Theme::test.phtml');
        $this->assertEquals(
            'RENDERED',
            $block->toHtml(),
            "The test block should be rendered\n\nThis is to make sure the assertions in the template were executed"
        );
    }

    /**
     * Re-register themes from the magentoComponentsDir fixture and set current theme to Hyva/test
     */
    private function registerTestTheme(): void
    {
        ThemeFixture::registerTestThemes();
        ThemeFixture::setCurrentTheme('Hyva/test');
    }

    private function createTemplate(string $templateFile, string $templateContents): void
    {
        $templatePath = __DIR__ . '/_files/design/frontend/Hyva/test/' . $templateFile . '';
        \file_put_contents(
            $templatePath,
            $templateContents
        );
        $this->testTemplates[] = $templatePath;
    }

    private function createBlockWithTemplate(string $template): Template
    {
        $page = $this->pageFactory->create();
        $page->getLayout()->generateXml();
        /** @var Template $block */
        $block = $page->getLayout()->createBlock(
            Template::class,
            'test_view_model_container',
        );
        $block->setTemplate($template);
        return $block;
    }
}
