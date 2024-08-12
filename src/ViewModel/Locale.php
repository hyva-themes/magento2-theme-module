<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Locale\Resolver as LocaleResolver;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Locale implements ArgumentInterface
{
    /**
     * @var LocaleResolver
     */
    protected $localeResolver;

    public function __construct(
        LocaleResolver $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
    }

    /**
     * Get the store local
     *
     * @return string
     */
    public function getLocale(): string
    {
        return str_replace('_', '-', $this->localeResolver->getLocale());
    }
}
