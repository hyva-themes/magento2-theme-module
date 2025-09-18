<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\App\CacheInterface as Cache;
use Magento\Framework\Composer\ComposerInformation;

class HyvaMetadata
{
    public const PRODUCT_NAME  = 'Hyvä';

    public const CACHE_TAG = 'hyva-version';

    private ComposerInformation $composerInfo;
    private $cache;

    public function __construct(
        Cache $cache,
        ComposerInformation $composerInfo,
    ) {
        $this->cache = $cache;
        $this->composerInfo = $composerInfo;
    }

    public function getHyvaPackageVersion(): ?string
    {
        $version = $this->composerInfo->getInstalledMagentoPackages()['hyva-themes/magento2-theme-module']['version'] ?? null;
        ray($version);
        return $version;
    }

    public function getName(): string
    {
        return self::PRODUCT_NAME;
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
}
