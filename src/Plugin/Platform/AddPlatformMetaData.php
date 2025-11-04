<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Platform;

class AddPlatformMetaData
{
    public function afterGetModuleName($subject, string $result): string
    {
        // allow Adyen support to identify Hyvä Theme
        return $result . '-hyva';
    }
}
