<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\State as AppState;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class DeployMode implements ArgumentInterface
{
    /**
     * @var AppState
     */
    private $appState;

    public function __construct(AppState $appState)
    {
        $this->appState = $appState;
    }

    public function isDeveloperMode(): bool
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER;
    }
}
