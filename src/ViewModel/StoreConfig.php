<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class StoreConfig implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var AppState
     */
    private $appState;

    public function __construct(ScopeConfigInterface $scopeConfig, AppState $appState)
    {
        $this->scopeConfig = $scopeConfig;
        $this->appState = $appState;
    }

    public function getStoreConfig($value)
    {
        return $this->scopeConfig->getValue($value, 'store');
    }

    public function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
