<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Deploy\Package;

use Magento\Deploy\Package\Package;
use Magento\Deploy\Package\PackageFile;

class ExcludeTailwindPlugin
{
    /**
     * @param Package $subject
     * @param PackageFile[] $result
     * @return PackageFile[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFiles(
        Package $subject,
        array $result
    ): array {
        foreach ($result as $key => $file) {
            if (strpos($file->getFilePath(), 'tailwind/') === 0) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
