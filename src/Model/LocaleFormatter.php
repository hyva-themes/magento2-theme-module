<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Model;

use Magento\Framework\Locale\ResolverInterface as LocalResolverInterface;

/**
 * Backwards compatible $localeFormatter template variable instance for Magento versions older than 2.4.5
 */
class LocaleFormatter
{
    /**
     * @var \NumberFormatter
     */
    private $numberFormatter;

    /**
     * @var LocalResolverInterface
     */
    private $localeResolver;

    /**
     * @param LocalResolverInterface $localeResolver
     */
    public function __construct(
        LocalResolverInterface $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
    }

    /**
     * @see \Magento\Framework\Locale\LocaleFormatter::getLocaleJs
     */
    public function getLocaleJs()
    {
        return str_replace("_", "-", $this->localeResolver->getLocale());
    }

    /**
     * @param string|float|int|null $number
     * @return false|string
     * @see \Magento\Framework\Locale\LocaleFormatter::formatNumber
     */
    public function formatNumber($number)
    {
        if (!is_float($number) && !is_int($number)) {
            $number = (int) $number;
        }

        if (!$this->numberFormatter) {
            $this->numberFormatter = numfmt_create($this->localeResolver->getLocale(), \NumberFormatter::TYPE_DEFAULT);
        }
        return $this->numberFormatter->format($number);
    }
}
