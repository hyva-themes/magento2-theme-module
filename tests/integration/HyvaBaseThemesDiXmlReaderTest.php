<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Service\HyvaBaseThemesDiXmlReader;
use Hyva\Theme\Service\HyvaThemes;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class HyvaBaseThemesDiXmlReaderTest extends TestCase
{
    private function getFixtureFile(string $name): string
    {
        return __DIR__ . '/_files/di-xml-reader/' . $name;
    }

    /** @test */
    #[Test]
    public function reads_built_in_base_themes_from_the_real_module_configuration()
    {
        /** @var HyvaBaseThemesDiXmlReader $reader */
        $reader = Bootstrap::getObjectManager()->create(HyvaBaseThemesDiXmlReader::class);

        $baseThemes = $reader->readBaseThemesFromDiXmlSources();

        $this->assertArrayHasKey('Hyva/reset', $baseThemes);
        $this->assertArrayHasKey('Hyva/default', $baseThemes);
        $this->assertArrayHasKey('Hyva/default-csp', $baseThemes);
        $this->assertTrue($baseThemes['Hyva/default']);
    }

    /** @test */
    #[Test]
    public function merges_custom_base_themes_with_later_files_overriding_earlier_ones()
    {
        /** @var HyvaBaseThemesDiXmlReader $reader */
        $reader = Bootstrap::getObjectManager()->create(HyvaBaseThemesDiXmlReader::class);

        $baseThemes = $reader->mergeBaseThemesFromDiXmlFiles([
            $this->getFixtureFile('custom-base-theme-di.xml'),
            $this->getFixtureFile('override-base-theme-di.xml'),
        ]);

        $this->assertTrue($baseThemes['Custom/base'], 'Custom base theme from the first file should be present');
        $this->assertTrue($baseThemes['Other/base'], 'Base theme declared with a leading backslash type name');
        $this->assertFalse(
            $baseThemes['Custom/disabled-base'],
            'The second file should override the item declared in the first file'
        );
        $this->assertTrue($baseThemes['Hyva/default'], 'Built-in base themes should always be part of the result');
    }

    /** @test */
    #[Test]
    public function ignores_files_without_the_argument_and_unparseable_files()
    {
        /** @var HyvaBaseThemesDiXmlReader $reader */
        $reader = Bootstrap::getObjectManager()->create(HyvaBaseThemesDiXmlReader::class);

        $baseThemes = $reader->mergeBaseThemesFromDiXmlFiles([
            $this->getFixtureFile('unrelated-di.xml'),
            $this->getFixtureFile('malformed-di.xml'),
            $this->getFixtureFile('missing-file-di.xml'),
        ]);

        $this->assertSame(
            ['Hyva/reset' => true, 'Hyva/default' => true, 'Hyva/default-csp' => true],
            $baseThemes,
            'Only the built-in base themes should remain when no file contributes items'
        );
    }

    /** @test */
    #[Test]
    public function hyva_themes_service_recovers_base_themes_from_the_reader_when_null_is_injected()
    {
        $objectManager = Bootstrap::getObjectManager();
        $reader = new class (
            $objectManager->get(ComponentRegistrar::class),
            $objectManager->get(\Magento\Framework\App\DeploymentConfig::class),
            $objectManager->get(\Psr\Log\LoggerInterface::class)
        ) extends HyvaBaseThemesDiXmlReader {
            /**
             * Test double returning a fixed configuration instead of scanning di.xml sources
             *
             * @return bool[]
             */
            public function readBaseThemesFromDiXmlSources(): array
            {
                return ['Hyva/default' => true, 'Custom/base' => true, 'Custom/disabled-base' => false];
            }
        };

        $hyvaThemes = new HyvaThemes(
            null,
            $objectManager->get(ComponentRegistrar::class),
            $objectManager->get(Filesystem::class),
            $reader
        );

        $this->assertSame(['Hyva/default', 'Custom/base'], $hyvaThemes->getHyvaBaseThemes());
    }
}
