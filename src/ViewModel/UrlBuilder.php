<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class UrlBuilder implements ArgumentInterface
{
    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    )
    {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve Store URL by path
     *
     * @param string $path
     * @return string
     */
    public function getUrl(string $path)
    {
        return $this->urlBuilder->getUrl($path);
    }
}
