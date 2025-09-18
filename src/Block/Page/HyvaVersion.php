<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Page;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\Template;

class HyvaVersion extends Template
{
    private ?string $version = null;
    private Filesystem $filesystem;

    public function __construct(
        Template\Context $context,
        Filesystem $filesystem,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->filesystem = $filesystem;
    }

    private function getComposerLockData(): ?array
    {
        try {
            $rootDirectory = $this->filesystem->getDirectoryRead(DirectoryList::ROOT);

            if (!$rootDirectory->isReadable('composer.lock')) {
                return null;
            }

            $composerLockContents = $rootDirectory->readFile('composer.lock');
            $composerLockData = json_decode($composerLockContents, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            return $composerLockData;
        } catch (FileSystemException $e) {
            return null;
        }
    }

    private function loadHyvaVersion(): ?string
    {
        $composerLock = $this->getComposerLockData();

        if ($composerLock === null) {
            return null;
        }

        foreach ($composerLock['packages'] ?? [] as $package) {
            if (($package['name'] ?? null) === 'hyva-themes/magento2-theme-module') {
                return $package['version'] ?? null;
            }
        }

        return null;
    }

    public function getHyvaVersion(): ?string
    {
        if ($this->version === null) {
            $this->version = $this->loadHyvaVersion();
        }
        return $this->version;
    }

    protected function _toHtml(): string
    {
        if ($this->getHyvaVersion() === null) {
            return '';
        }
        return parent::_toHtml();
    }
}
