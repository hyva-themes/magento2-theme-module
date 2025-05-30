<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
    /** @var StoreManagerInterface */
    private $storeManager;
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

    public function getPictureHtml(string $path, $width, $height, array $attributes = []): string
    {
        $images = [
            'default' => [
                'path' => $path,
                'width' => $width,
                'height' => $height,
            ]
        ];

        return $this->mediaHtmlProvider->getPictureHtml($images, $attributes);
    }

    public function getResponsivePictureHtml(array $images, array $attributes = []): string
    {
        return $this->mediaHtmlProvider->getPictureHtml($images, $attributes);
    }

}
