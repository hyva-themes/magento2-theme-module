<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\StubViewModels;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class SingleIdentity implements ArgumentInterface, IdentityInterface
{
    public const TAGS = ['single_identity'];

    public function getIdentities()
    {
        return self::TAGS;
    }
}
