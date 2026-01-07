<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
