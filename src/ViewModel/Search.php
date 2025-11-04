<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Search\Helper\Data as SearchDataHelper;

class Search implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var SearchDataHelper
     */
    private $searchDataHelper;

    /**
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param SearchDataHelper $searchDataHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfigInterface,
        SearchDataHelper $searchDataHelper
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        $this->searchDataHelper = $searchDataHelper;
    }

    /**
     * Retrieve minimum query length
     *
     * @param null|int|string $store
     * @return int
     */
    public function getMinQueryLength($store = null): int
    {
        return (int)$this->searchDataHelper->getMinQueryLength($store);
    }

    /**
     * @return int
     */
    public function getAutoCompleteLimit(): int
    {
        return $this->scopeConfigInterface->getValue('catalog/search/autocomplete_limit')
            ? (int)$this->scopeConfigInterface->getValue('catalog/search/autocomplete_limit')
            : 8;
    }

    /**
     * @param null|string $query
     * @return string
     */
    public function getResultUrl($query = null): string
    {
        return $this->searchDataHelper->getResultUrl($query);
    }

    /**
     * @return string
     */
    public function getQueryParamName(): string
    {
        return $this->searchDataHelper->getQueryParamName();
    }

    /**
     * Retrieve HTML escaped search query
     *
     * @return string
     */
    public function getEscapedQueryText(): string
    {
        return $this->searchDataHelper->getEscapedQueryText();
    }

    /**
     * Retrieve maximum query length
     *
     * @param null|int|string $store
     * @return int
     */
    public function getMaxQueryLength($store = null): int
    {
        return (int)$this->searchDataHelper->getMaxQueryLength($store);
    }
}
