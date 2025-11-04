<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Model\LocaleFormatter as HyvaLocaleFormatter;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Locale\LocaleFormatter as MagentoLocaleFormatter;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Result\PageFactory;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
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

    /** @var null|string[] */
    private $testTemplates;

    /**
     * @var ?string */
    private static $realProductVersion;

    public static function setUpBeforeClass(): void
    {
        self::$realProductVersion = ObjectManager::getInstance()->get(ProductMetadata::class)->getVersion();
        parent::setUpBeforeClass();
    }

    public static function tearDownAfterClass(): void
    {
        $magentoVersion = self::$realProductVersion;
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadata::class);
        (function () use ($magentoVersion) {
            $this->version = $magentoVersion;
        })->call($productMetadata);

        parent::tearDownAfterClass();
    }

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
     * Re-register themes from the magentoComponentsDir fixture and set current theme to Hyva/integration-test
     */
    private function registerTestTheme(): void
    {
        ThemeFixture::registerTestThemes();
        ThemeFixture::setCurrentTheme('Hyva/integration-test');
    }

    private function createTemplate(string $templateFile, string $templateContents): void
    {
        $templatePath = __DIR__ . '/_files/design/frontend/Hyva/integration-test/' . $templateFile . '';
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

    /**
     * @test
     */
    public function view_model_container_is_available_as_template_variable(): void
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
    public function global_view_models_are_accessible_through_container_in_template(): void
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

    public static function magentoVersionProvider(): array
    {
        return [
            '2.4.5' => ['2.4.5', class_exists(MagentoLocaleFormatter::class) ? MagentoLocaleFormatter::class : HyvaLocaleFormatter::class],
            '2.4.4' => ['2.4.4', HyvaLocaleFormatter::class]
        ];
    }

    /**
     * @test
     * @dataProvider magentoVersionProvider
     */
    public function locale_formatter_is_available_as_template_variable(string $targetMagentoVersion, string $expectedClass): void
    {
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadata::class);
        (function () use ($targetMagentoVersion) {
            $this->version = $targetMagentoVersion;
        })->call($productMetadata);

        $this->createTemplate(
            'Hyva_Theme/templates/test.phtml',
            <<<PHTML
            <?php
            \PHPUnit\Framework\Assert::assertTrue(isset(\$localeFormatter), 'The \$localeFormatter variable should be set in template');
            \PHPUnit\Framework\Assert::assertInstanceOf(
                $expectedClass::class,
                \$localeFormatter,
                'In Magento $targetMagentoVersion the \$localeFormatter variable should be instance of $expectedClass'
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
}
