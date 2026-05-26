<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\ViewModel\HyvaCsp;
use Hyva\Theme\ViewModel\ThemeLibrariesConfig;
use Magento\Csp\Model\Policy\FetchPolicy;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface as ThemeFallbackResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @covers ThemeLibrariesConfig
 */
class ThemeLibrariesConfigViewModelTest extends TestCase
{
    private function stubFilesystemDriverPool(): DriverPool
    {
        $driverPoolStub = $this->createStub(DriverPool::class);
        $driverPoolStub->method('getDriver')->willReturn($this->createStub(DriverInterface::class));
        return $driverPoolStub;
    }

    public function testReturnsEmptyArrayIfConfigNotPresent(): void
    {
        $themeFallbackStub = $this->createStub(ThemeFallbackResolver::class);

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'themeFallbackResolver' => $themeFallbackStub,
        ]);

        $themeFallbackStub->method('resolve')->willReturn(false);

        $this->assertEqualsCanonicalizing([], $sut->getThemeLibrariesConfig());
    }

    public function testReturnsThemeConfigAsArray(): void
    {
        $themeFallbackStub = $this->createStub(ThemeFallbackResolver::class);
        $filesystemDriverPoolStub = $this->stubFilesystemDriverPool();

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolStub,
            'themeFallbackResolver' => $themeFallbackStub,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackStub->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertEqualsCanonicalizing($config, $sut->getThemeLibrariesConfig());
    }

    public function testReturnsNullIfConfigPresentButNoSettingForGivenKey(): void
    {
        $themeFallbackStub = $this->createStub(ThemeFallbackResolver::class);
        $filesystemDriverPoolStub = $this->stubFilesystemDriverPool();

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolStub,
            'themeFallbackResolver' => $themeFallbackStub,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackStub->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertNull($sut->getVersionIdFor('foo'));
    }

    public static function unsafeEvalAllowedDataProvider(): array
    {
        return [
            'unsafe eval allowed' => [true, '3'],
            'unsafe eval forbidden' => [false, '3-csp'],
        ];
    }

    /**
     * @dataProvider unsafeEvalAllowedDataProvider
     */
    #[DataProvider('unsafeEvalAllowedDataProvider')]
    public function testReturnsVersionIfConfigPresentForGivenKeyDependingOnUnsafeEval(bool $isUnsafeEvalAllowed, string $expectedAlpineVersion): void
    {
        $themeFallbackStub = $this->createStub(ThemeFallbackResolver::class);
        $filesystemDriverPoolStub = $this->stubFilesystemDriverPool();
        $hyvaCspStub = $this->createStub(HyvaCsp::class);
        $unsafeEvalFetchPolicyStub = $this->createStub(FetchPolicy::class);
        $unsafeEvalFetchPolicyStub->method('isEvalAllowed')->willReturn($isUnsafeEvalAllowed);
        $hyvaCspStub->method('getScriptSrcPolicy')->willReturn($unsafeEvalFetchPolicyStub);

        $sut = ObjectManager::getInstance()->create(ThemeLibrariesConfig::class, [
            'filesystemDriverPool'  => $filesystemDriverPoolStub,
            'themeFallbackResolver' => $themeFallbackStub,
            'hyvaCsp'               => $hyvaCspStub,
        ]);

        $config = ['alpine' => '3'];

        $themeFallbackStub->method('resolve')->willReturn(ThemeLibrariesConfig::CONFIG_FILE_PATH);
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('fileGetContents')->willReturn(json_encode($config));
        $filesystemDriverPoolStub->getDriver(DriverPool::FILE)->method('isExists')->willReturn(true);

        $this->assertSame($expectedAlpineVersion, $sut->getVersionIdFor('alpine'));
    }
}
