<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\ThemeLibrariesConfig;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface as ThemeFallbackResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers ThemeLibrariesConfig
 */
class ThemeLibrariesConfigViewModelTest extends TestCase
{
    /**
     * @return DriverPool|MockObject
     */
    private function mockFilesystemDriverPool(): DriverPool
    {
        $driverPoolMock = $this->createMock(DriverPool::class);
        $driverPoolMock->method('getDriver')->willReturn($this->createMock(DriverInterface::class));
        return $driverPoolMock;
    }

    public function testReturnsEmptyArrayIfConfigNotPresent(): void
    {
        $themeFallbackMock = $this->createMock(ThemeFallbackResolver::class);

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'themeFallbackResolver' => $themeFallbackMock,
        ]);

        $themeFallbackMock->method('resolve')->willReturn(false);

        $this->assertEqualsCanonicalizing([], $sut->getThemeLibrariesConfig());
    }

    public function testReturnsThemeConfigAsArray(): void
    {
        $themeFallbackMock        = $this->createMock(ThemeFallbackResolver::class);
        $filesystemDriverPoolMock = $this->mockFilesystemDriverPool();

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolMock,
            'themeFallbackResolver' => $themeFallbackMock,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackMock->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertEqualsCanonicalizing($config, $sut->getThemeLibrariesConfig());
    }

    public function testReturnsNullIfConfigPresentButNoSettingForGivenKey(): void
    {
        $themeFallbackMock        = $this->createMock(ThemeFallbackResolver::class);
        $filesystemDriverPoolMock = $this->mockFilesystemDriverPool();

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolMock,
            'themeFallbackResolver' => $themeFallbackMock,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackMock->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertNull($sut->getVersionIdFor('foo'));
    }

    public function testReturnsVersionIfConfigPresentButNoSettingForGivenKey(): void
    {
        $themeFallbackMock        = $this->createMock(ThemeFallbackResolver::class);
        $filesystemDriverPoolMock = $this->mockFilesystemDriverPool();

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolMock,
            'themeFallbackResolver' => $themeFallbackMock,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackMock->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolMock->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertSame('3', $sut->getVersionIdFor('alpine'));
    }
}
