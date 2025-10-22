<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
