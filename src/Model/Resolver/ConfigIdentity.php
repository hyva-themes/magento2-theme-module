<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model\Resolver;

use Magento\Framework\App\Config;
use Magento\Framework\GraphQl\Query\Resolver\IdentityInterface;

class ConfigIdentity implements IdentityInterface
{
    /** @var string */
    protected $cacheTag = Config::CACHE_TAG;

    /**
     * @param array $resolvedData
     * @return string[]
     */
    public function getIdentities(array $resolvedData): array
    {
        $ids = [$this->cacheTag];

        return $ids;
    }
}
