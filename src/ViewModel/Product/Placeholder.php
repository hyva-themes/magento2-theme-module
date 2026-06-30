<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Product;

use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Psr\Log\LoggerInterface;

class Placeholder implements ArgumentInterface
{
    private PlaceholderFactory $placeholderFactory;
    private UrlInterface $urlBuilder;
    private LoggerInterface $logger;

    public function __construct(
        PlaceholderFactory $placeholderFactory,
        UrlInterface $urlBuilder,
        LoggerInterface $logger
    ) {
        $this->placeholderFactory = $placeholderFactory;
        $this->urlBuilder = $urlBuilder;
        $this->logger = $logger;
    }

    /**
     * Returns the placeholder image URL for a given type (e.g. image, thumbnail, small_image).
     *
     * @see \Magento\Catalog\Helper\Image::getDefaultPlaceholderUrl()
     */
    public function getPlaceholderUrl(string $type = 'image'): string
    {
        try {
            return $this->placeholderFactory->create(['type' => $type])->getUrl();
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->urlBuilder->getUrl('', ['_direct' => 'core/index/notFound']);
        }
    }

    /**
     * Returns a gallery-compatible image data object with placeholder URLs for all three sizes.
     */
    public function getPlaceholderImages(): array
    {
        return [new DataObject([
            'small_image_url'  => $this->getPlaceholderUrl('thumbnail'),
            'medium_image_url' => $this->getPlaceholderUrl('image'),
            'large_image_url'  => $this->getPlaceholderUrl('image'),
        ])];
    }
}
