<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\File\FileList;

use Magento\Framework\View\File\FileList\Collator;

/**
 * View file list collator
 *
 * Overwritten to prevent errors for `layout/override/base` layout xml files.
 * If a layout override exists for a module that's not active/installed, we
 * just want to ignore that.
 */
class CollatorPlugin extends Collator
{
    /**
     * Collate view files
     *
     * @param Collator $subject
     * @param \Closure $next
     * @param \Magento\Framework\View\File[] $files
     * @param \Magento\Framework\View\File[] $filesOrigin
     * @return \Magento\Framework\View\File[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCollate(Collator $subject, \Closure $next, $files, $filesOrigin)
    {
        foreach ($files as $file) {
            $identifier = $file->getFileIdentifier();
            if (!array_key_exists($identifier, $filesOrigin)) {
                // here we removed the LogicException
                continue;
            }
            $filesOrigin[$identifier] = $file;
        }
        return $filesOrigin;
    }
}
