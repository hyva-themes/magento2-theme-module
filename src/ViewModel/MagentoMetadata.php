<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class MagentoMetadata implements ArgumentInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    public function versionCompare(string $operator, string $compareToVersion): bool
    {
        return version_compare($this->productMetadata->getVersion(), $compareToVersion, $operator);
    }

    public function getVersion(): string
    {
        return $this->productMetadata->getVersion();
    }

    public function getName(): string
    {
        return $this->productMetadata->getName();
    }

    public function getEdition(): string
    {
        return $this->productMetadata->getEdition();
    }

    /**
     * Returns "Magento" for magento/project-*-edition, or a name like "Mage-OS" for other distributions.
     */
    public function getDistributionName(): string
    {
        return method_exists($this->productMetadata, 'getDistributionName')
            ? $this->productMetadata->getDistributionName()
            : $this->productMetadata->getName();
    }
}
