<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use function array_combine as zip;
use Magento\Catalog\Block\Product\View\Options\Type\Select\CheckableFactory;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Type\Date as DateCustomOptionConfig;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class CustomOption implements ArgumentInterface
{
    /**
     * @var string
     */
    protected $checkableTemplate = 'Magento_Catalog::product/composite/fieldset/options/view/checkable.phtml';

    /**
     * @var string
     */
    protected $multipleTemplate = 'Magento_Catalog::product/composite/fieldset/options/view/multiple.phtml';

    /**
     * @var CheckableFactory
     */
    private $checkableFactory;

    /**
     * @var DateCustomOptionConfig
     */
    private $dateCustomOptionConfig;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        CheckableFactory $checkableFactory,
        DateCustomOptionConfig $dateCustomOptionConfig,
        Escaper $escaper
    ) {
        $this->checkableFactory = $checkableFactory;
        $this->dateCustomOptionConfig = $dateCustomOptionConfig;
        $this->escaper = $escaper;
    }

    /**
     * Return html for control element
     *
     * @return string
     */
    public function getOptionHtml($option, $product): string
    {
        $optionType = $option->getType();

        $optionBlock = $this->checkableFactory->create();

        if ($optionType === Option::OPTION_TYPE_DROP_DOWN ||
            $optionType === Option::OPTION_TYPE_MULTIPLE
        ) {
            $optionBlock = $optionBlock->setTemplate($this->multipleTemplate);
        }

        if ($optionType === Option::OPTION_TYPE_RADIO ||
            $optionType === Option::OPTION_TYPE_CHECKBOX
        ) {
            $optionBlock = $optionBlock->setTemplate($this->checkableTemplate);
        }

        return $optionBlock
            ->setOption($option)
            ->setProduct($product)
            ->toHtml();
    }

    public function getDateDropdownsHtml(int $optionId, array $additionalSelectAttributes = []): string
    {
        $fieldsSeparator = '&nbsp;';
        $fieldsOrder = $this->dateCustomOptionConfig->getConfigData('date_fields_order') ?? '';
        $fieldsOrder = str_replace(',', $fieldsSeparator, $fieldsOrder);

        $monthsHtml = $this->getSelectFromToHtml($optionId, 'month', 1, 12, $additionalSelectAttributes['month'] ?? []);
        $daysHtml = $this->getSelectFromToHtml($optionId, 'day', 1, 31, $additionalSelectAttributes['day'] ?? []);

        $yearStart = (int) $this->dateCustomOptionConfig->getYearStart();
        $yearEnd = (int) $this->dateCustomOptionConfig->getYearEnd();
        $yearsHtml = $this->getSelectFromToHtml($optionId, 'year', $yearStart, $yearEnd, $additionalSelectAttributes['year'] ?? []);

        $translations = ['d' => $daysHtml, 'm' => $monthsHtml, 'y' => $yearsHtml];
        return '<div class="inline-block custom-option date-dropdown">' . strtr($fieldsOrder, $translations) . '</div>';
    }

    public function getTimeDropdownsHtml(int $optionId, array $additionalSelectAttributes = []): string
    {
        if ($this->is24hTimeFormat()) {
            $hourStart = 0;
            $hourEnd = 23;
            $dayPartHtml = '';
        } else {
            $hourStart = 1;
            $hourEnd = 12;
            '&nbsp;' . $dayPartHtml = $this->getSelectWithOptionsHtml($optionId, 'day_part', $this->getOptionsHtmlElements(
                [
                    'am' => $this->escaper->escapeHtml(__('AM')),
                    'pm' => $this->escaper->escapeHtml(__('PM'))
                ]
            ), $additionalSelectAttributes['day_part'] ?? []);
        }
        $hoursHtml = $this->getSelectFromToHtml($optionId, 'hour', $hourStart, $hourEnd, $additionalSelectAttributes['hour'] ?? []);
        $minutesHtml = $this->getSelectFromToHtml($optionId, 'minute', 0, 59, $additionalSelectAttributes['minute'] ?? []);

        return '<div class="inline-block custom-option time-dropdown">' . $hoursHtml . '<span class="time-seperator mx-1">:</span>' . $minutesHtml . $dayPartHtml . '</div>';
    }

    private function getSelectFromToHtml(int $optionId, string $name, int $from, int $to, array $additionalAttributes): string
    {
        return $this->getSelectWithOptionsHtml($optionId, $name, $this->getOptionsRangeHtml($from, $to), $additionalAttributes);
    }

    private function getSelectWithOptionsHtml(int $optionId, string $name, string $options, array $additionalAttributes): string
    {
        return sprintf(
            '<select name="options[%d][%s]" id="options_%d_%s" %s>%s</select>',
            $optionId,
            $name,
            $optionId,
            $name,
            implode(' ', $additionalAttributes),
            $options
        );
    }

    private function getOptionsRangeHtml(int $from, int $to): string
    {
        $values = range($from, $to);
        return $this->getOptionsHtmlElements(zip($values, $values));
    }

    private function getOptionsHtmlElements(array $xs): string
    {
        $options = '<option value="" >-</option>';
        foreach ($xs as $key => $val) {
            $options .= sprintf('<option value="%s" >%s</option>', $key, (is_numeric($val) ? sprintf('%02d', $val) : $val));
        }
        return $options;
    }

    public function is24hTimeFormat(): bool
    {
        return $this->dateCustomOptionConfig->is24hTimeFormat();
    }
}
