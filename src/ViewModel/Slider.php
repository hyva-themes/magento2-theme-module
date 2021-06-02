<?php declare(strict_types=1);
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

class Slider implements ArgumentInterface
{
    public const TEMPLATE_FILE = 'Magento_Theme::elements/slider-generic.phtml';
    public const ALL_DATA_JS_FN = '(data) => this.items = data';

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }

    public function getSliderForItems(
        string $itemTemplateFile,
        string $itemsJson,
        string $title = '',
        string $sliderTemplateFile = self::TEMPLATE_FILE
    ): AbstractBlock {
        $id = md5($itemsJson . $title . $sliderTemplateFile . $itemTemplateFile);

        $sliderBlock = $this->createTemplateBlock("slider.{$id}", [
            'slider_items_json' => $itemsJson,
            'title'             => $title,
            'template'          => $sliderTemplateFile,
        ]);

        $this->addSliderItemChildBlock($sliderBlock, $id, $itemTemplateFile);

        return $sliderBlock;
    }

    public function getSliderForQuery(
        string $itemTemplateFile,
        string $gqlQuery,
        string $queryDataProcessorJsFunction = self::ALL_DATA_JS_FN,
        string $title = '',
        string $sliderTemplateFile = self::TEMPLATE_FILE
    ): AbstractBlock {
        $id = md5($gqlQuery . $queryDataProcessorJsFunction . $sliderTemplateFile . $itemTemplateFile);

        $sliderBlock = $this->createTemplateBlock("slider.{$id}", [
            'graphql_query'           => $gqlQuery,
            'query_data_processor_js' => $queryDataProcessorJsFunction,
            'title'                   => $title,
            'template'                => $sliderTemplateFile,
        ]);

        $this->addSliderItemChildBlock($sliderBlock, $id, $itemTemplateFile);

        return $sliderBlock;
    }

    private function createTemplateBlock(string $name, array $arguments = []): Template
    {
        return $this->layout->createBlock(Template::class, $name, ['data' => $arguments]);
    }

    private function addSliderItemChildBlock(Template $sliderBlock, string $id, string $itemTemplateFile): void
    {
        $sliderBlock->setChild(
            'slider.item.template',
            $this->createTemplateBlock("slider.items.{$id}", ['template' => $itemTemplateFile])
        );
    }
}
