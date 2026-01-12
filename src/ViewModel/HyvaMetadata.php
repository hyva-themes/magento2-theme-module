<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
