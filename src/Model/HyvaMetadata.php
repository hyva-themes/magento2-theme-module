<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
        return $this->composerInfo->getInstalledMagentoPackages()['hyva-themes/magento2-theme-module']['version'] ?? null;
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
