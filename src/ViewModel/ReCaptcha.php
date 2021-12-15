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
use Magento\Store\Model\ScopeInterface;

class ReCaptcha implements ArgumentInterface
{
    const XML_CONFIG_PATH_RECAPTCHA = 'recaptcha_frontend/type_for/';

    const RECAPTCHA_INPUT_FIELD_BLOCK = 'recaptcha_input_field';

    const RECAPTCHA_V2_CHECKBOX_BLOCK = 'recaptcha_input_field_checkbox';

    const RECAPTCHA_V2_INVISIBLE_BLOCK = 'recaptcha_input_field_invisible';

    const RECAPTCHA_LEGAL_NOTICE_BLOCK = 'recaptcha_legal_notice';

    const RECAPTCHA_V2_CHECKBOX_VALIDATION_BLOCK = 'recaptcha_v2_checkbox_validation';

    const RECAPTCHA_V2_INVISIBLE_VALIDATION_BLOCK = 'recaptcha_v2_invisible_validation';

    const RECAPTCHA_V2_VALIDATION_PREFIX = 'recaptcha_v2';

    const RECAPTCHA_INPUT_FIELD = 'recaptcha_input_field';

    const RECAPTCHA_LEGAL_NOTICE = 'recaptcha_legal_notice';

    const XML_PATH_V2_CHECKBOX_PUBLIC_KEY = 'recaptcha_frontend/type_recaptcha/public_key';

    const XML_PATH_V2_INVISIBLE_PUBLIC_KEY = 'recaptcha_frontend/type_invisible/public_key';

    const XML_PATH_V3_INVISIBLE_PUBLIC_KEY = 'recaptcha_frontend/type_recaptcha_v3/public_key';

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
        $config = $this->scopeConfig->getValue(
            self::XML_CONFIG_PATH_RECAPTCHA . $key,
            ScopeInterface::SCOPE_STORE
        );

        if (!$config) {
            return null;
        }

        return [
            self::RECAPTCHA_INPUT_FIELD => $this->getRecaptchaInputField($config),
            self::RECAPTCHA_LEGAL_NOTICE => $this->getLegalNotice($config),
            self::RECAPTCHA_V2_VALIDATION_PREFIX => $this->getJavaScriptValidator($config),
        ];
    }

    /**
     * @return string
     */
    public function getRecaptchaInputField(string $config): string
    {
        return self::RECAPTCHA_INPUT_FIELD_BLOCK  . "_{$config}";
    }

    /**
     * @return string
     */
    public function getRecaptchaV2CheckboxBlock(string $config): string
    {
        return self::RECAPTCHA_V2_CHECKBOX_BLOCK . "_{$config}";
    }

    /**
     * @return string
     */
    public function getRecaptchaV2IvisibleBlock(string $config): string
    {
        return self::RECAPTCHA_V2_INVISIBLE_BLOCK . "_{$config}";
    }

    /**
     * @return string
     */
    public function getJavaScriptValidator(string $config): string
    {
        return self::RECAPTCHA_V2_VALIDATION_PREFIX . "_{$config}";
    }

    /**
     * @return string
     */
    public function getLegalNotice(string $config): string
    {
        return self::RECAPTCHA_LEGAL_NOTICE_BLOCK . "_{$config}";
    }

    /**
     * @return string
     */
    public function getV2CheckboxSiteKey(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_V2_CHECKBOX_PUBLIC_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getV2InvisibleSiteKey(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_V2_INVISIBLE__PUBLIC_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    /**
     * @return string
     */
    public function getV3InvisibleSiteKey(): string
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_V3_INVISIBLE__PUBLIC_KEY,
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
