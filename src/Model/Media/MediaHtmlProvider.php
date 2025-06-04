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
    private StoreManagerInterface $storeManager;
    private Escaper $escaper;

    public function __construct(
        StoreManagerInterface $storeManager,
        Escaper $escaper
    ) {
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * @param array $images Array of image configurations
     * @param array $attributes Common HTML attributes for the img tag
     * @return string
     */
    public function getPictureHtml(array $images, array $attributes = []): string
    {
        $sourceTags = [];
        $fallbackImage = null;

        foreach ($images as $image) {
            if (!isset($image['path'])) {
                continue;
            }

            if (isset($image['media-query'])) {
                $sourceTags[] = $this->buildSourceTag([
                    'media' => $image['media-query'],
                    'srcset' => $this->getMediaUrl($image['path'])
                ]);
            }

            if ($fallbackImage === null) {
                $fallbackImage = $image;
            }
        }

        if ($fallbackImage === null) {
            throw new InvalidArgumentException('No valid images provided');
        }

        $imgAttributes = $this->buildImageAttributes($fallbackImage, $attributes);
        $imgTag = $this->buildImgTag($imgAttributes);

        return '<picture>' . implode('', array_reverse($sourceTags)) . $imgTag . '</picture>';
    }

    private function buildImageAttributes(array $image, array $attributes): array
    {
        $imgAttributes = [
            'src' => $this->getMediaUrl($image['path']),
            'width' => $image['width'] ?? null,
            'height' => $image['height'] ?? null,
            'loading' => $attributes['lazy'] ?? true ? 'lazy' : null,
            'fetchpriority' => $attributes['fetch-priority'] ?? self::FETCH_PRIORITY_AUTO,
            'alt' => $attributes['alt'] ?? '',
        ];

        if (!empty($attributes['classes'])) {
            $imgAttributes['class'] = $this->escaper->escapeHtmlAttr($attributes['classes']);
        }

        return array_filter($imgAttributes, function ($value) {
            return $value !== null && $value !== '';
        });
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

    private function buildHtmlAttributes(array $attributes): string
    {
        $attributeString = '';
        foreach ($attributes as $name => $value) {
            $attributeString .= sprintf('%s="%s" ', $name, $this->escaper->escapeHtmlAttr($value));
        }
        return trim($attributeString);
    }
}
