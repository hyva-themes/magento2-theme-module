<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
