<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use function array_column as pick;
use function array_filter as filter;
use Hyva\Theme\Model\LocaleFormatter as HyvaLocaleFormatter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Locale\LocaleFormatter as MagentoLocaleFormatter;
use PHPUnit\Framework\TestCase;

class LocaleFormatterTest extends TestCase
{
    private function getPublicMethods(string $class): array
    {
        $reflectionMethods = (new \ReflectionClass($class))->getMethods(\ReflectionMethod::IS_PUBLIC);
        return filter(pick($reflectionMethods, 'name'), function (string $method): bool {
            return $method !== '__construct';
        });
    }

    private function getMagentoVersion(): string
    {
        return ObjectManager::getInstance()->get(ProductMetadata::class)->getVersion();
    }

    public function testProvidesCoreLocaleFormatterMethods(): void
    {
        $productMetadata = ObjectManager::getInstance()->get(ProductMetadata::class);
        $expectedMethods = version_compare($productMetadata->getVersion(), '2.4.5', '<')
            ? ['getLocaleJs', 'formatNumber']
            : $this->getPublicMethods(MagentoLocaleFormatter::class);

        $sut = ObjectManager::getInstance()->create(HyvaLocaleFormatter::class);
        foreach ($expectedMethods as $method) {
            $this->assertIsCallable([$sut, $method]);
        }
    }

    public function testHasIdenticalBehavior(): void
    {
        if (version_compare($this->getMagentoVersion(), '2.4.5', '<')) {
            $this->markTestSkipped('Unable to run test on Magento versions < 2.4.5');
        }
        $sut = ObjectManager::getInstance()->create(HyvaLocaleFormatter::class);
        $original = ObjectManager::getInstance()->create(MagentoLocaleFormatter::class);

        $this->assertSame($original->getLocaleJs(), $sut->getLocaleJs());
        $this->assertSame($original->formatNumber(null), $sut->formatNumber(null));
    }
}
