<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Group;
use Magento\Store\Model\Store as StoreModel;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Reimplemented from @see \Magento\Store\Block\Switcher
 */
class StoreSwitcher implements ArgumentInterface
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var bool */
    protected $storeInUrl;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var array $rawGroups */
    protected $rawGroups;

    /** @var Group[] $groups */
    protected $groups;

    /** @var array $rawStores */
    protected $rawStores;

    /** @var StoreModel[] $stores */
    protected $stores;

    /** @var StoreInterface $store */
    protected $store;

    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get raw groups.
     *
     * @return array
     */
    public function getRawGroups()
    {
        if (!$this->rawGroups) {
            $websiteGroups = $this->storeManager->getWebsite()->getGroups();

            $groups = [];
            foreach ($websiteGroups as $group) {
                $groups[$group->getId()] = $group;
            }
            $this->rawGroups = $groups;
        }
        return $this->rawGroups;
    }

    /**
     * Get raw stores.
     *
     * @return array
     */
    public function getRawStores()
    {
        if (!$this->rawStores) {
            $websiteStores = $this->storeManager->getWebsite()->getStores();
            $stores = [];
            foreach ($websiteStores as $store) {
                /* @var $store StoreModel */
                if (!$store->isActive()) {
                    continue;
                }
                $localeCode = $this->scopeConfig->getValue(
                    Data::XML_PATH_DEFAULT_LOCALE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                );
                $store->setLocaleCode($localeCode);
                $params = ['_query' => []];
                if (!$this->isStoreInUrl()) {
                    $params['_query']['___store'] = $store->getCode();
                }
                $baseUrl = $store->getUrl('', $params);

                $store->setHomeUrl($baseUrl);
                $stores[$store->getGroupId()][$store->getId()] = $store;
            }
            $this->rawStores = $stores;
        }
        return $this->rawStores;
    }

    /**
     * Retrieve list of store groups with default urls set
     *
     * @return Group[]
     */
    public function getGroups()
    {
        if (!$this->groups) {
            $rawGroups = $this->getRawGroups();
            $rawStores = $this->getRawStores();

            $groups = [];
            $localeCode = $this->scopeConfig->getValue(
                Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            foreach ($rawGroups as $group) {
                /* @var $group Group */
                if (!isset($rawStores[$group->getId()])) {
                    continue;
                }
                if ($group->getId() == $this->getCurrentGroupId()) {
                    $groups[] = $group;
                    continue;
                }

                $store = $group->getDefaultStoreByLocale($localeCode);

                if ($store) {
                    $group->setHomeUrl($store->getHomeUrl());
                    $groups[] = $group;
                }
            }
            $this->groups = $groups;
        }
        return $this->groups;
    }

    /**
     * Get stores.
     *
     * @return StoreModel[]
     */
    public function getStores()
    {
        if (!$this->stores) {
            $rawStores = $this->getRawStores();

            $groupId = $this->getCurrentGroupId();
            if (!isset($rawStores[$groupId])) {
                $stores = [];
            } else {
                $stores = $rawStores[$groupId];
            }
            $this->stores = $stores;
        }
        return $this->stores;
    }

    /**
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore()
    {
        if (!$this->store) {
            $this->store = $this->storeManager->getStore();
        }
        return $this->store;
    }

    /**
     * Get current group Id.
     *
     * @return int|null|string
     */
    public function getCurrentGroupId()
    {
        return $this->getStore()->getGroupId();
    }

    /**
     * Is store in url.
     *
     * @return bool
     */
    public function isStoreInUrl()
    {
        if ($this->storeInUrl === null) {
            $this->storeInUrl = $this->getStore()->isUseStoreInUrl();
        }
        return $this->storeInUrl;
    }
}
