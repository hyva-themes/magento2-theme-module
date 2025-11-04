<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Validator\Ip as IpValidator;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Cookie implements ArgumentInterface
{
    /**
     * Session config
     *
     * @var ConfigInterface
     */
    protected $sessionConfig;

    /**
     * @var IpValidator
     */
    protected $ipValidator;

    /**
     * @param ConfigInterface $cookieConfig
     * @param IpValidator $ipValidator
     */
    public function __construct(
        ConfigInterface $cookieConfig,
        IpValidator $ipValidator
    ) {
        $this->sessionConfig = $cookieConfig;
        $this->ipValidator = $ipValidator;
    }

    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->sessionConfig->getCookieDomain();

        if ($this->ipValidator->isValid($domain)) {
            return $domain;
        }

        if (!empty($domain[0]) && $domain[0] !== '.') {
            $domain = '.' . $domain;
        }
        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->sessionConfig->getCookiePath();
    }

    /**
     * Get configured cookie lifetime
     *
     * @return int
     */
    public function getLifetime()
    {
        return $this->sessionConfig->getCookieLifetime();
    }

    public function getCookieSecure(): bool
    {
        return (bool) $this->sessionConfig->getCookieSecure();
    }
}
