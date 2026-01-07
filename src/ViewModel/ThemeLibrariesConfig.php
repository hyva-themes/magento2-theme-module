<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\ObjectManager;
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

    /**
     * @var HyvaCsp
     */
    private $hyvaCsp;

    public function __construct(
        DesignInterface       $design,
        ThemeFallbackResolver $themeFallbackResolver,
        FilesystemDriverPool  $filesystemDriverPool,
        ?HyvaCsp              $hyvaCsp = null
    ) {
        $this->design = $design;
        $this->themeFileResolver = $themeFallbackResolver;
        $this->fileSystem = $filesystemDriverPool->getDriver(RulePool::TYPE_FILE);
        $this->hyvaCsp = $hyvaCsp ?? ObjectManager::getInstance()->get(HyvaCsp::class);
    }

    private function getThemeLibrariesConfigFile(ThemeInterface $theme): ?string
    {
        return $this->themeFileResolver->resolve(RulePool::TYPE_FILE, self::CONFIG_FILE_PATH, $theme->getArea(), $theme) ?: null;
    }

    public function getThemeLibrariesConfig(?ThemeInterface $theme = null): array
    {
        $file = $this->getThemeLibrariesConfigFile($theme ?? $this->design->getDesignTheme());

        return $file && $this->fileSystem->isExists($file)
            ? json_decode($this->fileSystem->fileGetContents($file), true) ?? []
            : [];
    }

    public function getVersionIdFor(string $library): ?string
    {
        $version = $this->getThemeLibrariesConfig()[$library] ?? null;
        if (null === $version) {
            return null;
        }

        if ('alpine' === $library) {
            return $this->appendCspIfRequired($version);
        }

        return $version;
    }

    private function appendCspIfRequired(string $version): string
    {
        // No CSP version exist for v2
        if ('2' === $version) {
            return $version;
        }

        // CSP already specified by library config
        if (substr($version, -4) === '-csp') {
            return $version;
        }

        // Evaluations are allowed, no need for Alpine CSP
        if ($this->hyvaCsp->getScriptSrcPolicy()->isEvalAllowed()) {
            return $version;
        }

        // Alpine CSP is required for this page
        return $version . '-csp';
    }
}
