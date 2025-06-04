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
    public const FETCH_PRIORITY_AUTO = 'auto';
    public const FETCH_PRIORITY_HIGH = 'high';
    public const FETCH_PRIORITY_LOW = 'low';

    /**
     * @param array<string, array{
     *     path: string,
     *     type?: string,
     *     width?: int,
     *     height?: int,
     *     media-query?: string,
     * }> $images
     *
     * @param array{
     *     alt?: string,
     *     lazy?: bool,
     *     classes?: string,
     *     fetch-priority?: string,
     * } $attributes
     *
     * @return string
     */
    public function getPictureHtml(array $images, array $attributes = []): string;
}
