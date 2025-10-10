<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Block\Adminhtml\Page;

use Hyva\Theme\Model\HyvaMetadata;
use Magento\Backend\Block\Template;

class Footer extends Template
{
    private $hyvaMetadata;

    public function __construct(
        Template\Context $context,
        HyvaMetadata $hyvaMetadata,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->hyvaMetadata = $hyvaMetadata;
    }

    public function getVersion(): string
    {
        return $this->hyvaMetadata->getVersion();
    }

    protected function _toHtml(): string
    {
        if ($this->getVersion() === null) {
            return '';
        }
        return parent::_toHtml();
    }
}
