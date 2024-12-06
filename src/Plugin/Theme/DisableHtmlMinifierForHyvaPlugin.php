<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Magento\Framework\App\Area;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReadFactory;
use Magento\Framework\Filesystem\Directory\WriteInterface as DirectoryWrite;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\Template\Html\Minifier;

class DisableHtmlMinifierForHyvaPlugin
{
    /**
     * @var DirectoryReadFactory
     */
    private $dirReadFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var DirectoryWrite|null
     */
    private $memoizedOutputDir;

    /**
     * @var array|null
     */
    private $memoizedHyvaThemePaths;

    public function __construct(
        Filesystem $filesystem,
        DirectoryReadFactory $dirReadFactory,
        ComponentRegistrar $componentRegistrar,
        ThemeProviderInterface $themeProvider
    ) {
        $this->filesystem = $filesystem;
        $this->dirReadFactory = $dirReadFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->themeProvider = $themeProvider;
    }

    private function getOutputDirectory(): DirectoryWrite
    {
        if (!isset($this->memoizedOutputDir)) {
            $this->memoizedOutputDir = $this->filesystem->getDirectoryWrite(DirectoryList::TMP_MATERIALIZATION_DIR);
        }
        return $this->memoizedOutputDir;
    }

    public function aroundMinify(Minifier $subject, callable $proceed, string $file): void
    {
        if ($this->isHyvaThemeFile($file)) {
            $this->copyUnminifiedFileToOutputDir($file);
            return;
        }
        $proceed($file);
    }

    /**
     * phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
     * phpcs:disable Magento2.Functions.DiscouragedFunction.DiscouragedWithAlternative
     */
    private function copyUnminifiedFileToOutputDir(string $file): void
    {
        $outputDir = $this->getOutputDirectory();
        if (!$outputDir->isExist()) {
            $outputDir->create();
        }

        $fileName = basename($file);
        $content = $this->dirReadFactory->create(dirname($file))->readFile($fileName);
        $relativePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getRelativePath($file);
        $outputDir->writeFile($relativePath, $content !== null ? rtrim($content) : '');
    }

    private function isHyvaThemeFile(string $file): bool
    {
        foreach ($this->getHyvaThemePaths() as $themePath) {
            if (substr($file, 0, strlen($themePath)) === $themePath) {
                return true;
            }
        }
        return false;
    }

    private function getHyvaThemePaths(): array
    {
        if (!isset($this->memoizedHyvaThemePaths)) {
            $this->memoizedHyvaThemePaths = $this->buildHyvaThemePathsList();
        }
        return $this->memoizedHyvaThemePaths;
    }

    private function buildHyvaThemePathsList(): array
    {
        $hyvaThemePaths = [];
        foreach ($this->componentRegistrar->getPaths(ComponentRegistrar::THEME) as $themePath => $fsPath) {
            if ($this->isHyvaTheme($themePath)) {
                $hyvaThemePaths[] = $fsPath;
            }
        }
        return $hyvaThemePaths;
    }

    private function isHyvaTheme(string $themePath): bool
    {
        $theme = $this->themeProvider->getThemeByFullPath($themePath);
        while ($theme && $theme->getArea() === Area::AREA_FRONTEND) {
            if (strpos($theme->getCode(), 'Hyva/') === 0) {
                return true;
            }
            $theme = $theme->getParentTheme();
        }
        return false;
    }
}
