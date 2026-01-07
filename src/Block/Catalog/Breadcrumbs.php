<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Catalog;

use Magento\Catalog\Block\Breadcrumbs as CatalogBreadcrumbs;
use Magento\Catalog\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template\Context;

class Breadcrumbs extends CatalogBreadcrumbs
{
    /**
     * @var ScopeConfigInterface
     */
    private $config;

    public function __construct(Context $context, Data $catalogData, ScopeConfigInterface $config, array $data = [])
    {
        parent::__construct($context, $catalogData, $data);
        $this->config = $config;
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->config->getValue('catalog/hyva_breadcrumbs/client_side_enable', 'store')) {
            $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
            $clientSideRenderedCrumbsTemplate = $this->getData('client_side_rendered_crumbs_template');
            if ($breadcrumbs && $clientSideRenderedCrumbsTemplate) {
                $breadcrumbs->setTemplate($clientSideRenderedCrumbsTemplate);
            }
        }
        return $this;
    }

    /**
     * This "product_breadcrumbs" block conditionally sets properties on the regular "breadcrumbs" block from the Magento_Theme module.
     *
     * @see \Hyva\Theme\Block\Catalog\Breadcrumbs::_prepareLayout()
     */
    public function toHtml()
    {
        return '';
    }
}
