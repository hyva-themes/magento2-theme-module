<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

namespace Hyva\Theme\Model\Template;

use Hyva\Theme\ViewModel\SvgIcons;

/**
 * Template Filter Model with icon directve functionality
 * We use the implicit functionality of Magento\Framework\Filter\DirectiveProcessor\LegacyDirective for parsing
 * and processing because it is not possible to inject a new custom directive processor via di.xml.
 * The parameter directiveProcessors is missing in Magento\Widget\Model\Template\Filter::__construct()
 * and must be passed to the parent class - so hardcoded defaults will be used instead of configured values
 *
 */
class Filter extends \Magento\Widget\Model\Template\Filter
{

    protected $svgIcons;

    /**
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Variable\Model\VariableFactory $coreVariableFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Framework\UrlInterface $urlModel
     * @param \Pelago\Emogrifier $emogrifier
     * @param \Magento\Variable\Model\Source\Variables $configVariables
     * @param \Magento\Widget\Model\ResourceModel\Widget $widgetResource
     * @param \Magento\Widget\Model\Widget $widget
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Stdlib\StringUtils $string,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Variable\Model\VariableFactory $coreVariableFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\UrlInterface $urlModel,
        \Pelago\Emogrifier $emogrifier,
        \Magento\Variable\Model\Source\Variables $configVariables,
        \Magento\Widget\Model\ResourceModel\Widget $widgetResource,
        \Magento\Widget\Model\Widget $widget,
        SvgIcons $svgIcons)
    {
        parent::__construct(
            $string,
            $logger,
            $escaper,
            $assetRepo,
            $scopeConfig,
            $coreVariableFactory,
            $storeManager,
            $layout,
            $layoutFactory,
            $appState,
            $urlModel,
            $emogrifier,
            $configVariables,
            $widgetResource,
            $widget
        );
        $this->svgIcons = $svgIcons;
    }

    /**
     * render a svg icon
     *
     * @param string[] $construction
     * @return string
     */
    public function iconDirective($construction)
    {
        $params = $this->getParameters(html_entity_decode($construction[2], ENT_QUOTES));

        //if no path provided -> remove the directive code
        if (!$path = $params['path']) {
            return '';
        }
        $classes = $params['classes'] ?? '';
        $width = isset($params['width']) ? (int)$params['width'] : null;
        $height = isset($params['height']) ? (int)$params['height'] : null;

        return $this->svgIcons->renderHtml($path, $classes, $width, $height);
    }
}
