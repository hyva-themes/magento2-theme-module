<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Customer;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Customer\Controller\Ajax\Login as AjaxLoginController;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\Result\JsonFactory as JsonResultFactory;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

class AjaxLoginFormkeyValidationPlugin
{
    /**
     * @var FormKeyValidator
     */
    private $formKeyValidator;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var JsonResultFactory
     */
    private $jsonResultFactory;

    public function __construct(
        FormKeyValidator $formKeyValidator,
        HttpRequest $request,
        JsonResultFactory $jsonResultFactory,
        CurrentTheme $currentTheme
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->request = $request;
        $this->currentTheme = $currentTheme;
        $this->jsonResultFactory = $jsonResultFactory;
    }

    public function aroundExecute(AjaxLoginController $subject, callable $proceed)
    {
        if ($this->currentTheme->isHyva() && ! $this->validateFormKey()) {
            $jsonResult = $this->jsonResultFactory->create();
            $jsonResult->setData(['errors' => true, 'message' => (string) __('Could not authenticate. Please try again later')]);
            return $jsonResult;
        }
        return $proceed();
    }

    private function validateFormKey(): bool
    {
        $data = json_decode($this->request->getContent(), true) ?: [];
        $this->request->setParam('form_key', $data['formKey'] ?? null);
        return $this->formKeyValidator->validate($this->request);
    }
}
