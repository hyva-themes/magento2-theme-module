<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Theme;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Layout;
use Magento\Theme\Controller\Result\JsFooterPlugin;

class DisableJsFooterPlugin
{
    /**
     * @var HttpRequest
     */
    private $httpRequest;

    /**
     * @var string[]
     */
    private $disableOnActions;

    public function __construct(HttpRequest $httpRequest, array $disableMoveToFooterOnActions = [])
    {
        $this->httpRequest      = $httpRequest;
        $this->disableOnActions = array_values($disableMoveToFooterOnActions);
    }

    /**
     * @param JsFooterPlugin $subject
     * @param callable $continue
     * @param Layout $origSubject
     * @param Layout $origResult
     * @param ResponseInterface $httpResponse
     * @return Layout|null
     */
    public function aroundAfterRenderResult(JsFooterPlugin $subject, callable $continue, $origSubject, $origResult, $httpResponse)
    {
        $isMoveToFooterDisabledOnCurrentRoute = in_array(strtolower($this->httpRequest->getFullActionName()), $this->disableOnActions);
        return $isMoveToFooterDisabledOnCurrentRoute
            ? $origResult
            : $continue($origSubject, $origResult, $httpResponse);
    }
}
