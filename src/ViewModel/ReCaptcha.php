<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class ReCaptcha implements ArgumentInterface
{
    const XML_CONFIG_PATH_RECAPTCHA = 'recaptcha_frontend/type_for/';

    const RECAPTCHA_INPUT_FIELD_BLOCK = 'recaptcha_input_field';

    const RECAPTCHA_V2_CHECKBOX_BLOCK = 'recaptcha_v2_checkbox';

    const RECAPTCHA_V2_INVISIBLE_BLOCK = 'recaptcha_v2_invisible';

    const RECAPTCHA_LEGAL_NOTICE_BLOCK = 'recaptcha_legal_notice';

    const RECAPTCHA_INPUT_FIELD = 'recaptcha_input_field';

    const RECAPTCHA_LEGAL_NOTICE= 'recaptcha_legal_notice';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $key
     * @return string[]|null
     */
    public function getRecaptchaData(string $key): ?array
    {
        if (!$this->scopeConfig->getValue(self::XML_CONFIG_PATH_RECAPTCHA . $key, 'store')) {
            return null;
        }
        return [
            self::RECAPTCHA_INPUT_FIELD => $this->getRecaptchaInputField(),
            self::RECAPTCHA_LEGAL_NOTICE    => $this->getLegalNotice(),
        ];
    }

    /**
     * @return string
     */
    private function getRecaptchaInputField(): string
    {
        return self::RECAPTCHA_INPUT_FIELD_BLOCK;
    }

    /**
     * @return string
     */
    private function getRecaptchaV2Checkbox(): string
    {
        return self::RECAPTCHA_V2_CHECKBOX_BLOCK;
    }

    /**
     * @return string
     */
    private function getRecaptchaV2Ivisible(): string
    {
        return self::RECAPTCHA_V2_INVISIBLE_BLOCK;
    }

    /**
     * @return string
     */
    private function getLegalNotice(): string
    {
        return self::RECAPTCHA_LEGAL_NOTICE_BLOCK;
    }
}
