<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\View\Asset;
use Magento\Framework\View\DesignInterface;

/**
 * This class exists to offer autocompletion, it could have been a virtual type otherwise
 */
class HeroiconsOutline extends SvgIcons implements Heroicons
{
    private const HEROICONS_OUTLINE = 'heroicons/outline';

    public function __construct(
        Asset\Repository $assetRepository,
        CacheInterface $cache,
        DesignInterface $design
    ) {
        parent::__construct($assetRepository, $cache, $design, self::HEROICONS_OUTLINE);
    }
}
