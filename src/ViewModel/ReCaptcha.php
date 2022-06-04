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
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;

class ReCaptcha implements ArgumentInterface
{
    const RECAPTCHA_INPUT_FIELD = 'recaptcha_input_field';

    const RECAPTCHA_INPUT_FIELD_BLOCK = 'recaptcha_input_field';

    const RECAPTCHA_LEGAL_NOTICE = 'recaptcha_legal_notice';

    const RECAPTCHA_LEGAL_NOTICE_BLOCK = 'recaptcha_legal_notice';

    const RECAPTCHA_LOADER = 'recaptcha_loader';

    const RECAPTCHA_LOADER_BLOCK = 'recaptcha_loader';

    const RECAPTCHA_SCRIPT_TOKEN = 'recaptcha_script_token';

    const RECAPTCHA_SCRIPT_TOKEN_BLOCK = 'recaptcha_validation';

    const XML_CONFIG_PATH_RECAPTCHA = 'recaptcha_frontend/type_for/';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LayoutInterface $layout
    ) {
        $this->scopeConfig               = $scopeConfig;
        $this->layout                    = $layout;
    }

    public function getInputHtml(string $formId, string $recaptchaInputId = ''): string
    {
        $data = $this->getRecaptchaData($formId);
        return $data && $this->layout->hasElement($data[self::RECAPTCHA_INPUT_FIELD])
            ? $this->layout->getBlock($data[self::RECAPTCHA_INPUT_FIELD])
                           ->setData('form_id', $formId)
                           ->setData('input_element_id', $recaptchaInputId)
                           ->toHtml()
            : '';
    }

    public function getLegalNoticeHtml(string $formId): string
    {
        $data = $this->getRecaptchaData($formId);
        return $data && $this->layout->hasElement($data[self::RECAPTCHA_LEGAL_NOTICE])
            ? $this->layout->getBlock($data[self::RECAPTCHA_LEGAL_NOTICE])->setData('form_id', $formId)->toHtml()
            : '';
    }

    public function getValidationJsHtml(string $formId, string $recaptchaInputId = ''): string
    {
        $data = $this->getRecaptchaData($formId);
        return $data && $this->layout->hasElement($data[self::RECAPTCHA_SCRIPT_TOKEN])
            ? $this->layout->getBlock($data[self::RECAPTCHA_SCRIPT_TOKEN])
                           ->setData('form_id', $formId)
                           ->setData('input_element_id', $recaptchaInputId)
                           ->toHtml()
            : '';
    }

    public function calcJsInstanceSuffix(string $formId): string
    {
        return ucfirst(str_replace(['-', '_', ' ', '.'], '', $formId));
    }

    /**
     * @param string $formId
     * @return string|null One of 'recaptcha', 'invisible', 'recaptcha_v3', '' or null
     */
    private function getSelectedTypeForForm(string $formId): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_PATH_RECAPTCHA . $formId, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Deprecated as a public method, now intended for internal use only.
     *
     * Instead, use getInputHtml, getLegalNoticeHtml and getValidationJsHtml.
     *
     * @param string $formId
     * @return string[]|null
     */
    public function getRecaptchaData(string $formId): ?array
    {
        $recaptchaType = $this->getSelectedTypeForForm($formId);

        if (!$recaptchaType) {
            return null;
        }

        return [
            // Renders the DOM nodes that capture the reCaptcha result
            self::RECAPTCHA_INPUT_FIELD  => $this->getInputFieldBockName($recaptchaType),
            // Renders the legal notice for reCaptcha v3
            self::RECAPTCHA_LEGAL_NOTICE => $this->getLegalNoticeBlockName($recaptchaType),
            // Renders the JS that triggers the validation
            self::RECAPTCHA_SCRIPT_TOKEN => $this->getScriptTokenBlockName($recaptchaType, $formId),
        ];
    }

    private function getInputFieldBockName(string $type): string
    {

        return $type === 'recaptcha_v3'
            ? self::RECAPTCHA_INPUT_FIELD_BLOCK
            : self::RECAPTCHA_INPUT_FIELD_BLOCK . "_{$type}";
    }

    private function getLegalNoticeBlockName(string $type): string
    {
        return $type === 'recaptcha_v3'
            ? self::RECAPTCHA_LEGAL_NOTICE_BLOCK
            : '';
    }

    private function getScriptTokenBlockName(string $type, string $formId): string
    {
        if ($type !== 'recaptcha_v3') {
            return self::RECAPTCHA_SCRIPT_TOKEN_BLOCK . "_{$type}";
        }
        // For backward compatibility:
        // Honor the special case block names for customer_edit, customer_login and newsletter,
        // in case they are declared in child themes
        if ($this->layout->hasElement(self::RECAPTCHA_SCRIPT_TOKEN_BLOCK . "_{$formId}")) {
            return self::RECAPTCHA_SCRIPT_TOKEN_BLOCK . "_{$formId}";
        }

        return self::RECAPTCHA_SCRIPT_TOKEN_BLOCK;
    }
}
