<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Filesystem\DriverInterface as FilesystemDriver;
use Magento\Framework\Filesystem\DriverPool as FilesystemDriverPool;
use Magento\Framework\View\Design\Fallback\RulePool;
use Magento\Framework\View\Design\FileResolution\Fallback\ResolverInterface as ThemeFallbackResolver;
use Magento\Framework\View\Design\ThemeInterface;
use Magento\Framework\View\DesignInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ThemeLibrariesConfig implements ArgumentInterface
{
    public const CONFIG_FILE_PATH = 'etc/hyva-libraries.json';

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ThemeFallbackResolver
     */
    private $themeFileResolver;

    /**
     * @var FilesystemDriver
     */
    private $fileSystem;

    public function __construct(
        DesignInterface $design,
        ThemeFallbackResolver $themeFallbackResolver,
        FilesystemDriverPool $filesystemDriverPool
    ) {
        $this->design            = $design;
        $this->themeFileResolver = $themeFallbackResolver;
        $this->fileSystem        = $filesystemDriverPool->getDriver(RulePool::TYPE_FILE);
    }

    private function getThemeLibrariesConfigFile(ThemeInterface $theme): ?string
    {
        return $this->themeFileResolver->resolve(RulePool::TYPE_FILE, self::CONFIG_FILE_PATH, $theme->getArea(), $theme) ?: null;
    }

    public function getThemeLibrariesConfig(ThemeInterface $theme = null): array
    {
        $file = $this->getThemeLibrariesConfigFile($theme ?? $this->design->getDesignTheme());

        return $file && $this->fileSystem->isExists($file)
            ? json_decode($this->fileSystem->fileGetContents($file), true) ?? []
            : [];
    }

    public function getVersionIdFor(string $library): ?string
    {
        return $this->getThemeLibrariesConfig()[$library] ?? null;
    }
}
