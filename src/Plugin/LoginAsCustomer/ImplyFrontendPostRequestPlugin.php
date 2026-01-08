<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\LoginAsCustomer;

use Hyva\Theme\Service\CurrentTheme;
use Magento\Framework\App\PageCache\Version;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\View\Result\Page;
use Magento\LoginAsCustomerFrontendUi\Controller\Login\Index as LoginAsCustomerFrontendController;

class ImplyFrontendPostRequestPlugin
{
    /**
     * @var CurrentTheme
     */
    private $currentTheme;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var HttpRequest
     */
    private $httpRequest;

    public function __construct(
        CurrentTheme $currentTheme,
        HttpRequest $httpRequest,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->currentTheme = $currentTheme;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->httpRequest = $httpRequest;
    }

    public function afterExecute(LoginAsCustomerFrontendController $subject, ResultInterface $result)
    {
        if ($this->currentTheme->isHyva() && $result instanceof Page) {
            // On a successful "Login as customer" request, we need to set the private_content_version
            // cookie so the real section data is loaded and dispatched instead of the default.
            $this->setSectionDataVersionCookie();

        }
        return $result;
    }

    /**
     * @see \Magento\Framework\App\PageCache\Version
     */
    private function setSectionDataVersionCookie(): void
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                                                            ->setDuration(Version::COOKIE_PERIOD)
                                                            ->setPath('/')
                                                            ->setSecure($this->httpRequest->isSecure())
                                                            ->setHttpOnly(false)
                                                            ->setSameSite('Lax');
        $value = '1';
        $this->cookieManager->setPublicCookie(Version::COOKIE_NAME, $value, $publicCookieMetadata);
    }
}
