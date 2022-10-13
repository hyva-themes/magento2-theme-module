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

    public function __construct(
        ViewModelRegistry $viewModelRegistry,
        ProductMetadata $productMetadata,
        LocaleFormatterFactory $hyvaLocaleFormatterFactory
    ) {
        $this->viewModelRegistry          = $viewModelRegistry;
        $this->productMetadata            = $productMetadata;
        $this->hyvaLocaleFormatterFactory = $hyvaLocaleFormatterFactory;
    }

    /**
     * @param Php $subject
     * @param BlockInterface $block
     * @param $filename
     * @param array $dictionary
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeRender(Php $subject, BlockInterface $block, $filename, array $dictionary = [])
    {
        $dictionary['viewModels'] = $this->viewModelRegistry;
        if (! class_exists(MagentoLocaleFormatter::class) || version_compare($this->productMetadata->getVersion(), '2.4.5', '<')) {
            $dictionary['localeFormatter'] = $this->hyvaLocaleFormatterFactory->create();
        }
        return [$block, $filename, $dictionary];
    }
}
