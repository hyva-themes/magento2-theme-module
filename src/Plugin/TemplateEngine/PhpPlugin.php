<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\TemplateEngine;

use Hyva\Theme\Model\LocaleFormatterFactory;
use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\Theme\Service\CurrentTheme;
use Hyva\Theme\ViewModel\HyvaCsp;
use Magento\Framework\App\Area;
use Magento\Framework\App\AreaList as AppAreaList;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\State as AppState;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Locale\LocaleFormatter as MagentoLocaleFormatter;
use Magento\Framework\View\Element\BlockInterface;

use Magento\Framework\View\TemplateEngine\Php;

/**
 * Adds the viewModelRegistry to all template files as $viewModels
 */
class PhpPlugin
{
    /**
     * @var ViewModelRegistry
     */
    protected $viewModelRegistry;

    /**
     * @var ProductMetadata
     */
    private $productMetadata;

    /**
     * @var LocaleFormatterFactory
     */
    private $hyvaLocaleFormatterFactory;

    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var HyvaCsp
     */
    private $hyvaCsp;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    private ?AppAreaList $appAreaList;

    public function __construct(
        ViewModelRegistry $viewModelRegistry,
        ProductMetadata $productMetadata,
        LocaleFormatterFactory $hyvaLocaleFormatterFactory,
        CurrentTheme $currentTheme,
        ?HyvaCsp $hyvaCsp = null,
        ?AppState $appState = null, // keep for BC
        ?AppAreaList $appAreaList = null,
        ?HttpRequest $httpRequest = null
    ) {
        $this->viewModelRegistry = $viewModelRegistry;
        $this->productMetadata = $productMetadata;
        $this->hyvaLocaleFormatterFactory = $hyvaLocaleFormatterFactory;
        $this->currentTheme = $currentTheme;
        $this->hyvaCsp = $hyvaCsp ?? ObjectManager::getInstance()->get(HyvaCsp::class);
        $this->appAreaList = $appAreaList ?? ObjectManager::getInstance()->get(AppAreaList::class);
        $this->httpRequest = $httpRequest ?? ObjectManager::getInstance()->get(HttpRequest::class);
    }

    /**
     * @param Php $subject
     * @param BlockInterface $block
     * @param $filename
     * @param mixed[] $dictionary
     * @return mixed[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * Assign template variables that are available in all Hyvä templates.
     */
    public function beforeRender(Php $subject, BlockInterface $block, $filename, array $dictionary = [])
    {
        $dictionary = $this->addAllThemesTemplateVariables($dictionary);
        $dictionary = $this->addHyvaOnlyTemplateVariables($dictionary);
        $dictionary = $this->addAdminTemplateVariables($dictionary);

        return [$block, $filename, $dictionary];
    }

    private function addAllThemesTemplateVariables(array $dictionary): array
    {
        // The $viewModels variable has been available in all themes for a long time now, so it must not be removed.
        // New template variables should be assigned to Hyvä themes only to minimize potential conflicts.
        $dictionary['viewModels'] = $this->viewModelRegistry;

        return $dictionary;
    }

    private function addHyvaOnlyTemplateVariables(array $dictionary): array
    {
        if (!$this->currentTheme->isHyva()) {
            return $dictionary;
        }

        if (!class_exists(MagentoLocaleFormatter::class) || version_compare($this->productMetadata->getVersion(), '2.4.5', '<')) {
            $dictionary['localeFormatter'] = $this->hyvaLocaleFormatterFactory->create();
        }

        $dictionary['hyvaCsp'] = $this->hyvaCsp;

        return $dictionary;
    }

    private function addAdminTemplateVariables(array $dictionary): array
    {
        if ($this->appAreaList->getCodeByFrontName($this->httpRequest->getFrontName()) !== Area::AREA_ADMINHTML) {
            return $dictionary;
        }

        $dictionary['hyvaCsp'] = $this->hyvaCsp;

        return $dictionary;
    }
}
