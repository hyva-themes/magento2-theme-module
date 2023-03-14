<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\FrontController;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;
use Magento\Framework\Controller\ResultInterface;

/**
 * Adds a 'x-built-with: Hyva Themes' header to frontend requests
 * Removing or altering the header message violates the terms of usage.
 */
class HyvaHeaderPlugin
{
    private $theme;

    public function __construct(CurrentTheme $theme)
    {
        $this->theme = $theme;
    }

    /**
     * @param FrontControllerInterface $subject
     * @param ResponseInterface|ResultInterface $result
     * @return ResponseHttp|ResultInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(FrontControllerInterface $subject, $result)
    {
        if ($this->theme->isHyva() && (
                $result instanceof ResultInterface ||
                $result instanceof HttpInterface
            )
        ) {
            // This would be "Hyvä Themes", if utf-8 would be supported...
            $result->setHeader('x-built-with', 'Hyva Themes');
        }

        return $result;
    }
}
