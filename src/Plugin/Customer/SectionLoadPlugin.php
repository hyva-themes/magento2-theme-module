<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\Customer;

use Magento\Customer\Controller\Section\Load;
use Magento\Framework\App\PageCache\Version;

class SectionLoadPlugin extends Version
{
    /**
     * If we don't have a `private_content_version` cookie yet,
     * always create one on /customer/section/load requests
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeExecute(Load $subject)
    {
        if (!$this->cookieManager->getCookie(self::COOKIE_NAME)) {
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_PERIOD)
                ->setPath('/')
                ->setSecure($this->request->isSecure())
                ->setHttpOnly(false);
            $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $this->generateValue(), $publicCookieMetadata);
        }
    }
}
