<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Hyva\Theme\Model\Media;

use InvalidArgumentException;
use Magento\Framework\UrlInterface;
use Magento\Framework\Escaper;
use Magento\Store\Model\StoreManagerInterface;

class MediaHtmlProvider implements MediaHtmlProviderInterface
{
    private ?string $mediaBaseUrl = null;
    private StoreManagerInterface $storeManager;
    private Escaper $escaper;

    public function __construct(
        StoreManagerInterface $storeManager,
        Escaper $escaper
    ) {
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    public function getPictureHtml(array $images, array $imgAttributes = [], array $pictureAttributes = []): string
    {
        $nonFallbackSourceTags = [];
        $fallbackSourceTags = [];
        $fallbackImage = null;

        foreach ($images as $image) {
            if (!isset($image['path'])) {
                continue;
            }

            if (!isset($image['fallback'])) {
                $image['fallback'] = false;
            }

            if ($fallbackImage === null || $image['fallback'] === true) {
                $fallbackImage = $image;
            }

            if (isset($image['media'])) {
                $sourceAttributes = [
                    'media' => $image['media'],
                    'srcset' => $this->getMediaUrl($image['path'])
                ];

                if (isset($image['sizes'])) {
                    $sourceAttributes['sizes'] = $image['sizes'];
                }

                $sourceTag = $this->buildSourceTag($sourceAttributes);

                if ($image['fallback'] === true) {
                    $fallbackSourceTags[] = $sourceTag;
                } else {
                    $nonFallbackSourceTags[] = $sourceTag;
                }
            }
        }

        if (!isset($fallbackImage['media'])) {
            $fallbackSourceAttributes = [
              'srcset' => $this->getMediaUrl($fallbackImage['path'])
            ];

            if (isset($fallbackImage['sizes'])) {
                $fallbackSourceAttributes['sizes'] = $fallbackImage['sizes'];
            }

            $fallbackSourceTags[] = $this->buildSourceTag($fallbackSourceAttributes);
        }

        $finalImgAttributes = $this->buildImageAttributes($fallbackImage, $imgAttributes);
        $imgTag = $this->buildImgTag($finalImgAttributes);

        $allSourceTags = array_merge(array_reverse($nonFallbackSourceTags), $fallbackSourceTags);

        return $this->buildPictureTag($allSourceTags, $imgTag, $pictureAttributes);
    }

    private function buildImageAttributes(array $image, array $imgAttributes): array
    {
        $attributes = [];

        if (isset($image['path'])) {
            $attributes['src'] = $this->getMediaUrl($image['path']);
        }

        if (!isset($imgAttributes['sizes'])) {
            if (isset($image['width'])) {
                $attributes['width'] = (string)$image['width'];
            }

            if (isset($image['height'])) {
                $attributes['height'] = (string)$image['height'];
            }
        }

        foreach ($imgAttributes as $name => $value) {
            $attributes[$name] = $value;
        }

        return $attributes;
    }

    private function getMediaUrl(string $path): string
    {
        if ($this->mediaBaseUrl === null) {
            $this->mediaBaseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        }

        return $this->mediaBaseUrl . ltrim($path, '/');
    }

    private function buildImgTag(array $attributes): string
    {
        return '<img ' . $this->buildHtmlAttributes($attributes) . '>';
    }

    private function buildSourceTag(array $attributes): string
    {
        return '<source ' . $this->buildHtmlAttributes($attributes) . '>';
    }

    private function buildPictureTag(array $sourceTags, string $imgTag, array $pictureAttributes): string
    {
        $pictureAttributesHtml = $this->buildHtmlAttributes($pictureAttributes);
        $pictureOpenTag = $pictureAttributesHtml ? '<picture ' . $pictureAttributesHtml . '>' : '<picture>';

        return $pictureOpenTag . implode('', $sourceTags) . $imgTag . '</picture>';
    }

    private function buildHtmlAttributes(array $attributes): string
    {
        $attributeParts = [];
        foreach ($attributes as $name => $value) {
            $attributeParts[] = sprintf('%s="%s"', $name, $this->escaper->escapeHtmlAttr((string)$value));
        }
        return implode(' ', $attributeParts);
    }

    public function getImageUrl(string $imagePath, ?int $width = null, ?int $height = null): string
    {
        return $this->getMediaUrl($imagePath);
    }
}
