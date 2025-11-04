<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\Media\MediaHtmlProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class Media implements ArgumentInterface
{
    private StoreManagerInterface $storeManager;
    private MediaHtmlProviderInterface $mediaHtmlProvider;

    public function __construct(
        StoreManagerInterface $storeManager,
        MediaHtmlProviderInterface $mediaHtmlProvider
    ) {
        $this->storeManager = $storeManager;
        $this->mediaHtmlProvider = $mediaHtmlProvider;
    }

    public function getMediaUrl(): string
    {
        try {
            return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        } catch (NoSuchEntityException $e) {
            return '';
        }
    }

    /**
     * @param string $path
     * @param int|null $width
     * @param int|null $height
     * @param array<string, string> $imgAttributes Suggested attributes: alt, loading (lazy|eager),
     *                                              fetchpriority (auto|high|low), class, id, style,
     *                                              decoding (sync|async|auto)
     * @param array<string, string> $pictureAttributes Suggested attributes: class, id, style,
     *                                                 data-* attributes
     */
    public function getPictureHtml(
        string $path,
        ?int $width = null,
        ?int $height = null,
        array $imgAttributes = [],
        array $pictureAttributes = []
    ): string {
        $images = [
            'default' => [
                'path' => $path,
                'width' => $width,
                'height' => $height,
            ]
        ];

        return $this->mediaHtmlProvider->getPictureHtml($images, $imgAttributes, $pictureAttributes);
    }

    /**
     * @param array<string, array{
     *     path: string,
     *     type?: string,
     *     width?: int,
     *     height?: int,
     *     media?: string,
     *     fallback?: bool,
     * }> $images
     *
     * @param array<string, string> $imgAttributes Suggested attributes: alt, loading (lazy|eager),
     *                                              fetchpriority (auto|high|low), class, id, style,
     *                                              decoding (sync|async|auto), sizes, srcset
     * @param array<string, string> $pictureAttributes Suggested attributes: class, id, style,
     *                                                 data-* attributes
     */
    public function getResponsivePictureHtml(
        array $images,
        array $imgAttributes = [],
        array $pictureAttributes = []
    ): string {
        return $this->mediaHtmlProvider->getPictureHtml($images, $imgAttributes, $pictureAttributes);
    }
}
