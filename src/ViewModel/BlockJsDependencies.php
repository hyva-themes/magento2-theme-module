<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Register the given template of block dependencies on the block class.
 */
class BlockJsDependencies implements ArgumentInterface
{
    public const DEFAULT_PRIORITY = 10;

    public const HYVA_JS_BLOCK_DEPENDENCIES_KEY = 'hyva_js_block_dependencies';

    public const HYVA_JS_TEMPLATE_DEPENDENCIES_KEY = 'hyva_js_template_dependencies';

    public function setBlockTemplateDependency(AbstractBlock $dependantBlock, string $dependencyTemplate): void
    {
        $this->setBlockTemplateDependencyWithPriority($dependantBlock, $dependencyTemplate, null);
    }

    public function setBlockTemplateDependencyWithPriority(AbstractBlock $dependantBlock, string $dependencyTemplate, ?int $priority): void
    {
        $dependencies = $dependantBlock->getData(self::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY) ?? [];
        $dependencies[$dependencyTemplate] = $priority ?? self::DEFAULT_PRIORITY;
        $dependantBlock->setData(self::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY, $dependencies);
    }

    public function setBlockNameDependency(AbstractBlock $dependantBlock, string $dependencyBlockName): void
    {
        $this->setBlockNameDependencyWithPriority($dependantBlock, $dependencyBlockName, null);
    }

    public function setBlockNameDependencyWithPriority(AbstractBlock $dependantBlock, string $dependencyBlockName, ?int $priority): void
    {
        $dependencies = $dependantBlock->getData(self::HYVA_JS_BLOCK_DEPENDENCIES_KEY) ?? [];
        $dependencies[$dependencyBlockName] = $priority ?? self::DEFAULT_PRIORITY;
        $dependantBlock->setData(self::HYVA_JS_BLOCK_DEPENDENCIES_KEY, $dependencies);
    }
}
