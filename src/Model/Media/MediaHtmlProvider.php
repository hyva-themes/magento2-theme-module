<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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

    public function __construct(
        private readonly StoreManagerInterface $storeManager,
        private readonly Escaper $escaper
    ) {
    }

    public function getPictureHtml(array $images, array $imgAttributes = [], array $pictureAttributes = []): string
    {
        $sourceTags = [];
        $fallbackImage = null;

        foreach ($images as $image) {
            if (!isset($image['path'])) {
                continue;
            }

            if (isset($image['media'])) {
                $sourceAttributes = [
                    'media' => $image['media'],
                    'srcset' => $this->getMediaUrl($image['path'])
                ];

                if (isset($image['sizes'])) {
                    $sourceAttributes['sizes'] = $image['sizes'];
                }

                $sourceTags[] = $this->buildSourceTag($sourceAttributes);
            }

            if ($fallbackImage === null) {
                $fallbackImage = $image;
            }
        }

        if ($fallbackImage === null) {
            throw new InvalidArgumentException('No valid images provided');
        }

        if (!isset($fallbackImage['media'])) {
            $fallbackSourceAttributes = [
                'srcset' => $this->getMediaUrl($fallbackImage['path'])
            ];

            if (isset($fallbackImage['sizes'])) {
                $fallbackSourceAttributes['sizes'] = $fallbackImage['sizes'];
            }

            $sourceTags[] = $this->buildSourceTag($fallbackSourceAttributes);
        }

        $finalImgAttributes = $this->buildImageAttributes($fallbackImage, $imgAttributes);
        $imgTag = $this->buildImgTag($finalImgAttributes);

        return $this->buildPictureTag($sourceTags, $imgTag, $pictureAttributes);
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

        return $pictureOpenTag . implode('', array_reverse($sourceTags)) . $imgTag . '</picture>';
    }

    private function buildHtmlAttributes(array $attributes): string
    {
        $attributeParts = [];
        foreach ($attributes as $name => $value) {
            $attributeParts[] = sprintf('%s="%s"', $name, $this->escaper->escapeHtmlAttr((string)$value));
        }
        return implode(' ', $attributeParts);
    }
}
