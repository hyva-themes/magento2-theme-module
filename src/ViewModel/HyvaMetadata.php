<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Hyva\Theme\Model\HyvaMetadata as Metadata;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class HyvaMetadata implements ArgumentInterface
{
    private $hyvaMetadata;

    public function __construct(
        Metadata $hyvaMetadata
    ) {
        $this->hyvaMetadata = $hyvaMetadata;
    }

    public function getName(): string
    {
        return $this->hyvaMetadata->getName();
    }

    public function getVersion(): string
    {
        return $this->hyvaMetadata->getVersion();
    }
}
