<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
     *     media?: string,
     * }> $images
     *
     * @param array<string, string> $imgAttributes Suggested attributes: alt, loading (lazy|eager),
     *                                              fetchpriority (auto|high|low), class, id, style,
     *                                              decoding (sync|async|auto), sizes, srcset
     * @param array<string, string> $pictureAttributes Suggested attributes: class, id, style,
     *                                                 data-* attributes
     *
     * @return string
     */
    public function getPictureHtml(array $images, array $imgAttributes = [], array $pictureAttributes = []): string;

    /**
     * @param string $imagePath
     *
     * @return string
     */
    public function getImageUrl(string $imagePath, ?int $width = null, ?int $height = null): string;
}
