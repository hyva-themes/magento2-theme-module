<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Cart;

use Magento\Framework\Serialize\Serializer\JsonHexTag;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Checkout\Model\CompositeConfigProvider;

class CheckoutConfig implements ArgumentInterface
{
    /**
     * @var CompositeConfigProvider
     */
    protected $configProvider;

    /**
     * @var JsonHexTag
     */
    private $jsonHexTagSerializer;

    public function __construct(CompositeConfigProvider $configProvider, JsonHexTag $jsonHexTagSerializer)
    {
        $this->configProvider = $configProvider;
        $this->jsonHexTagSerializer = $jsonHexTagSerializer;
    }

    /**
     * Retrieve checkout configuration
     *
     * @return array
     */
    public function getCheckoutConfig(): array
    {
        return $this->configProvider->getConfig();
    }

    /**
     * Get Serialized Checkout Config
     *
     * @return bool|string
     */
    public function getSerializedCheckoutConfig()
    {
        return $this->jsonHexTagSerializer->serialize($this->getCheckoutConfig());
    }
}
