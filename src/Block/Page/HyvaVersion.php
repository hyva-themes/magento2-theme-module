<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Page;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Composer\ComposerInformation;
use Magento\Framework\View\Element\Template;

class HyvaVersion extends Template
{
    public const CACHE_TAG = 'hyva-version';

    private ComposerInformation $composerInfo;

    public function __construct(
        Template\Context $context,
        CacheInterface $cache,
        ComposerInformation $composerInfo,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cache = $cache;
        $this->composerInfo = $composerInfo;
    }

    public function getHyvaPackageVersion(): ?string
    {
        return $this->composerInfo->getInstalledMagentoPackages()['hyva-themes/magento2-theme-module']['version'] ?? null;
    }

    public function getVersion(): ?string
    {
        $cachedValue = $this->cache->load(self::CACHE_TAG);
        if ($cachedValue !== false) {
            return $cachedValue;
        }

        $version = $this->getHyvaPackageVersion();
        if ($version) {
            $this->cache->save($version, self::CACHE_TAG, [self::CACHE_TAG]);
            return $version;
        }

        return null;
    }

    protected function _toHtml(): string
    {
        if ($this->getVersion() === null) {
            return '';
        }
        return parent::_toHtml();
    }
}
