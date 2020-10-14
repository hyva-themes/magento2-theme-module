<?php
declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Footer implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    private function getStoreConfig($path)
    {
        return $this->scopeConfig->getValue($path, 'store');
    }

    public function getFooterCopyright(): string
    {
        return (string)$this->getStoreConfig('design/footer/copyright');
    }

}
