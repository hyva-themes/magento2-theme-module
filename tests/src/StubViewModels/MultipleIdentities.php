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

class MultipleIdentities implements ArgumentInterface, IdentityInterface
{
    public const TAGS = ['multiple_identities1', 'multiple_identities2'];

    public function getIdentities()
    {
        return self::TAGS;
    }
}
