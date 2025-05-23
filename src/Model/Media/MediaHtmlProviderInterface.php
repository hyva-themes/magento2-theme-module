<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */
declare(strict_types=1);

namespace Hyva\Theme\Model\Media;

interface MediaHtmlProviderInterface
{
    public function getPictureHtml(array $images, array $attributes = []): string;
}
