<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Logo;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context as TemplateBlockContext;
use Magento\MediaStorage\Helper\File\Storage\Database as DatabaseFileStorage;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

/**
 * This class provides forward compatibility for Magento versions < 2.4.3
 *
 * @see \Magento\Theme\ViewModel\Block\Html\Header\LogoPathResolver (added in 2.4.3)
 * @see \Magento\Sales\ViewModel\Header\LogoPathResolver (added in 2.4.3)
 */
class SalesLogoPathResolver extends LogoPathResolver
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    public function __construct(
        TemplateBlockContext $context,
        DatabaseFileStorage $fileStorageHelper,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $fileStorageHelper, $data);
    }

    /**
     * Return logo image path
     *
     * @return string|null
     * @see \Magento\Sales\ViewModel\Header\LogoPathResolver::getPath
     */
    public function getPath(): ?string
    {
        $path = null;
        $storeId = null;
        $order = $this->coreRegistry->registry('current_order');
        if ($order instanceof Order) {
            $storeId = $order->getStoreId();
        }
        $storeLogoPath = $this->_scopeConfig->getValue(
            'sales/identity/logo_html',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($storeLogoPath !== null) {
            $path = 'sales/store/logo_html/' . $storeLogoPath;
        }
        return $path;
    }
}
