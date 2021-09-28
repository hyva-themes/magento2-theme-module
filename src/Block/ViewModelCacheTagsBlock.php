<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block;

use Hyva\Theme\Model\ViewModelCacheTags;
use Magento\Framework\App\State as AppState;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Context;

/**
 * Block to pass view model identities collected via the view model registry to the page cache tags.
 */
class ViewModelCacheTagsBlock extends AbstractBlock implements IdentityInterface
{
    /**
     * @var ViewModelCacheTags
     */
    private $viewModelCacheTags;

    /**
     * @var AppState
     */
    private $appState;

    public function __construct(
        Context $context,
        ViewModelCacheTags $viewModelCacheTags,
        AppState $appState,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewModelCacheTags = $viewModelCacheTags;
        $this->appState           = $appState;
    }

    public function toHtml()
    {
        return $this->appState->getMode() === AppState::MODE_DEVELOPER
            ? sprintf('<!-- View Model Identities: %s -->', implode(', ', $this->getIdentities()))
            : '';
    }

    public function getIdentities()
    {
        return $this->viewModelCacheTags->get();
    }
}
