<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Logo;

use Magento\Config\Model\Config\Backend\Image\Logo;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * This class provides forward compatibility for Magento versions < 2.4.3
 *
 * @see \Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolver (added in 2.4.3)
 * @see \Magento\Sales\ViewModel\Header\LogoPathResolver (added in 2.4.3)
 */
class LogoPathResolver implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Return logo image path
     *
     * @return string|null
     */
    public function getPath(): ?string
    {
        $path = null;
        $storeLogoPath = $this->scopeConfig->getValue(
            'design/header/logo_src',
            ScopeInterface::SCOPE_STORE
        );
        if ($storeLogoPath !== null) {
            $path = Logo::UPLOAD_DIR . '/' . $storeLogoPath;
        }
        return $path;
    }
}
