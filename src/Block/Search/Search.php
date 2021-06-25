<?php
namespace Hyva\Theme\Block\Search;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Search extends \Magento\Framework\View\Element\Template
{
    protected $scopeConfigInterface;
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ScopeConfigInterface $scopeConfigInterface,
        array $data = []
    ) {
        $this->scopeConfigInterface = $scopeConfigInterface;
        parent::__construct($context, $data);
    }

    public function getAutoCompleteLimit()
    {
        return $this->scopeConfigInterface->getValue('catalog/search/autocomplete_limit') ? $this->scopeConfigInterface->getValue('catalog/search/autocomplete_limit') : 8;
    }
}
