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
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Theme\Block\Html\Header\Logo as LogoBlock;

/**
 * This class provides forward compatibility for Magento versions < 2.4.3
 *
 * @see \Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolver (added in 2.4.3)
 * @see \Magento\Sales\ViewModel\Header\LogoPathResolver (added in 2.4.3)
 */
class LogoPathResolver extends LogoBlock implements ArgumentInterface
{
    /**
     * Return logo image path
     *
     * @return string|null
     * @see \Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolver::getPath
     */
    public function getPath(): ?string
    {
        $path = null;
        $storeLogoPath = $this->_scopeConfig->getValue(
            'design/header/logo_src',
            ScopeInterface::SCOPE_STORE
        );
        if ($storeLogoPath !== null) {
            $path = Logo::UPLOAD_DIR . '/' . $storeLogoPath;
        }
        return $path;
    }

    /**
     * Override the parent block method to get rid of the code dependency on LogoPathResolverInterface
     *
     * The dependency breaks backward compatibility with Magento < 2.4.3
     *
     * @return string
     * @see \Magento\Theme\Block\Html\Header\Logo::_getLogoUrl
     */
    protected function _getLogoUrl()
    {
        $path = $this->getPath();
        if ($path !== null && $this->_isFile($path)) {
            $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $path;
        } elseif ($this->getLogoFile()) {
            $url = $this->getViewFileUrl($this->getLogoFile());
        } else {
            $url = $this->getViewFileUrl('images/logo.svg');
        }
        return $url;
    }
}
