<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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

    /**
     * @param Package $subject
     * @param array[] $result
     * @return array[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetMap(
        Package $subject,
        array $result
    ): array {
        foreach (array_keys($result) as $key) {
            if (strpos($key, 'tailwind/') === 0) {
                unset($result[$key]);
            }
        }
        return $result;
    }
}
