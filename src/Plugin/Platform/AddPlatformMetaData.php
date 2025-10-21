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
        return $result . '-' . strtolower($this->getSuffix());
    }

    private function getSuffix(): string
    {
        $className = get_class($this);
        $parts = explode('\\', $className);
        if (count($parts) >= 1) {
            return $parts[0];
        }
        return '';
    }
}
