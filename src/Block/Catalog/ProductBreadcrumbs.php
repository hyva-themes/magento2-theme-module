<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Catalog;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

class ProductBreadcrumbs extends \Magento\Theme\Block\Html\Breadcrumbs
{
    public const XML_PATH_PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';

    public const CACHE__CATEGORY_TREE_TAG = 'HYVA_CACHE_CATEGORY_TREE';

    public const XML_PATH_CLIENT_SIDE_BREADCRUMB = 'catalog/hyva_breadcrumbs/client_side_enable';

    private $cacheId = 'cacheCategoryTree';
 
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
 
    /**
     * @param Template\Context $context
     * @param SerializerInterface $serialized
     * @param Registry $coreRegistry
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SerializerInterface $serializer,
        \Magento\Framework\Registry $coreRegistry,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Json $json = null,
        array $data = []
    ) {
        parent::__construct($context, $data, $json);
        $this->serializer   = $serializer;
        $this->coreRegistry = $coreRegistry;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    protected function _prepareLayout(): Template
    {
        parent::_prepareLayout();
        if ($this->isEnableClientSideBreadcrumb()) {
            $this->setTemplate('Magento_Catalog::product/breadcrumbs.phtml');
        } else {
            $this->getLayout()->createBlock(\Magento\Catalog\Block\Breadcrumbs::class);
            $this->setTemplate('Magento_Theme::html/breadcrumbs.phtml');
        }
        return $this;
    }
    
    /**
     * Get data category tree from cache
     * @return string
     */
    public function loadDataCategoryTreeFromCache()
    {
        $data = $this->_cache->load($this->cacheId);
        if (!$data) {
            return $this->saveDataInsideCache();
        }
        return $data;
    }
    
    /**
     * Save data menu tree into cache
     * @return array
     */
    private function saveDataInsideCache()
    {
        $dataNagigation = $this->getViewModelNavigation()->getNavigation();
        $cleanDataNagigation = $this->updateData($dataNagigation);
        $data = $this->serializer->serialize($cleanDataNagigation);
        $data = str_replace("'", "\'", $data);
        $this->_cache->save($data, $this->cacheId, [self::CACHE__CATEGORY_TREE_TAG]);
        return $data;
    }
    
    /**
     * Get data menu tree
     *
     * @param array $dataNagigation
     * @return array
     */
    private function updateData(&$dataNagigation = [])
    {
        foreach ($dataNagigation as $index => &$menuItem) {
            $this->removeUnUsedValue($menuItem);
            if (count($menuItem['childData']) > 0) {
                $this->updateData($menuItem['childData']);
            }
        }
        return $dataNagigation;
    }

    /**
     * Remove some value which not use in array menu data
     *
     * @param array $menuItem
     */
    private function removeUnUsedValue(&$menuItem)
    {
        unset($menuItem['has_active']);
        unset($menuItem['image']);
        unset($menuItem['is_active']);
        unset($menuItem['is_parent_active']);
        unset($menuItem['is_category']);
        unset($menuItem['position']);
    }

    /**
     * Retrieve current Product object
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * Retrieve client side breadcrumb hyva for store
     * @return true|false
     */
    public function isEnableClientSideBreadcrumb()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_CLIENT_SIDE_BREADCRUMB,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @return string
     */
    public function getProductUrlSuffix()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_PRODUCT_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );
    }
}
