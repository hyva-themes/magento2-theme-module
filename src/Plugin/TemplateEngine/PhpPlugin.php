<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\TemplateEngine;

use Hyva\Theme\Model\LocaleFormatterFactory;
use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\Theme\Service\CurrentTheme;
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

    public function __construct(
        ViewModelRegistry $viewModelRegistry,
        ProductMetadata $productMetadata,
        LocaleFormatterFactory $hyvaLocaleFormatterFactory,
        CurrentTheme $currentTheme
    ) {
        $this->viewModelRegistry = $viewModelRegistry;
        $this->productMetadata = $productMetadata;
        $this->hyvaLocaleFormatterFactory = $hyvaLocaleFormatterFactory;
        $this->currentTheme = $currentTheme;
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
        if (! $this->currentTheme->isHyva()) {
            return $dictionary;
        }

        if (!class_exists(MagentoLocaleFormatter::class) || version_compare($this->productMetadata->getVersion(), '2.4.5', '<')) {
            $dictionary['localeFormatter'] = $this->hyvaLocaleFormatterFactory->create();
        }

        return $dictionary;
    }
}
