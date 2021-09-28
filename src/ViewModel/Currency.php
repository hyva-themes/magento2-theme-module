<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Directory\Model\Currency as CurrencyModel;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\Bundle\CurrencyBundle;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;

class Currency implements ArgumentInterface
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var CurrencyModel */
    protected $currencyModel;

    /** @var PostHelper */
    protected $postDataHelper;

    /** @var ResolverInterface */
    protected $localeResolver;

    /** @var array $currencies */
    protected $currencies;

    /** @var CurrencyModel $currentCurrency */
    protected $currentCurrency;

    /** @var Escaper $escaper */
    protected $escaper;

    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;

    public function __construct(
        StoreManagerInterface $storeManager,
        Escaper $escaper,
        UrlInterface $urlBuilder,
        CurrencyModel $currencyModel,
        PostHelper $postDataHelper,
        ResolverInterface $localeResolver
    ) {
        $this->storeManager = $storeManager;
        $this->currencyModel = $currencyModel;
        $this->postDataHelper = $postDataHelper;
        $this->localeResolver = $localeResolver;
        $this->escaper = $escaper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve count of currencies
     * Return 0 if only one currency
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getCurrencies());
    }

    /**
     * Retrieve currencies array
     * Return array: code => currency name
     * Return empty array if only one currency
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCurrencies()
    {
        if (!$this->currencies) {
            $currencies = [];
            $codes = $this->storeManager->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes) > 1) {
                $rates = $this->currencyModel->getCurrencyRates(
                    $this->storeManager->getStore()->getBaseCurrency()->getCode(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $allCurrencies = (new CurrencyBundle())->get(
                            $this->localeResolver->getLocale()
                        )['Currencies'];
                        $currencies[$code] = $allCurrencies[$code][1] ?: $code;
                    }
                }
            }

            $this->currencies = $currencies;
        }
        return $this->currencies;
    }

    /**
     * Retrieve Currency Switch URL
     *
     * @return string
     */
    public function getSwitchUrl()
    {
        return $this->urlBuilder->getUrl('directory/currency/switch');
    }

    /**
     * Return POST data for currency to switch
     *
     * @param string $code
     * @return string
     */
    public function getSwitchCurrencyPostData($code)
    {
        return $this->postDataHelper->getPostData(
            $this->escaper->escapeUrl($this->getSwitchUrl()),
            ['currency' => $code]
        );
    }

    /**
     * @return CurrencyModel
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrency()
    {
        if (!$this->currentCurrency) {
            $this->currentCurrency = $this->storeManager->getStore()->getCurrentCurrency();
        }
        return $this->currentCurrency;
    }

    /**
     * Retrieve Current Currency code
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrencyCode()
    {
        // do not use $this->storeManager->getStore()->getCurrentCurrencyCode() because of probability
        // to get an invalid (without base rate) currency from code saved in session
        return $this->getCurrentCurrency()->getCode();
    }

    /**
     * Retrieve Current Currency Symbol
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCurrentCurrencySymbol()
    {
        // do not use $this->storeManager->getStore()->getCurrentCurrencyCode() because of probability
        // to get an invalid (without base rate) currency from code saved in session
        return $this->getCurrentCurrency()->getCurrencySymbol();
    }
}
