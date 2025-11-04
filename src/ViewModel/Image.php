<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Helper\Image as ImageHelper;

class Image implements ArgumentInterface
{
    /**
     * @var ImageHelper
     */
    private $imageHelper;

    public function __construct(
        ImageHelper $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }

    /**
     * Return the Placeholder-image according to the imageType
     */
    public function getPlaceholderImageUrl(string $imageType): string
    {
        return $this->imageHelper->getDefaultPlaceholderUrl($imageType);
    }
}
