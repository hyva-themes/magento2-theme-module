<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Logo;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * This class provides forward compatibility for Magento versions < 2.4.3
 *
 * @see \Magento\Theme\ViewModel\Block\Html\Header\LogoSizeResolver (added in 2.4.3)
 */
class LogoSizeResolver implements ArgumentInterface
{
    /**
     * Logo width config path
     */
    private const XML_PATH_DESIGN_HEADER_LOGO_WIDTH = 'design/header/logo_width';

    /**
     * Logo height config path
     */
    private const XML_PATH_DESIGN_HEADER_LOGO_HEIGHT = 'design/header/logo_height';

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

    public function getWidth(?int $storeId = null): ?int
    {
        return $this->getConfig(self::XML_PATH_DESIGN_HEADER_LOGO_WIDTH, $storeId);
    }

    public function getHeight(?int $storeId = null): ?int
    {
        return $this->getConfig(self::XML_PATH_DESIGN_HEADER_LOGO_HEIGHT, $storeId);
    }

    /**
     * Get config value
     *
     * @param string $path
     * @param int|null $storeId
     * @return int|null
     */
    private function getConfig(string $path, ?int $storeId = null): ?int
    {
        $value = $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $value === null ? null : (int) $value;
    }
}
