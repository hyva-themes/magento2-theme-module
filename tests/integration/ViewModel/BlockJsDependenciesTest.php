<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class BlockJsDependenciesTest extends TestCase
{
    public function testRegistersBlockTemplateDependencies(): void
    {
        $objectManager = ObjectManager::getInstance();
        $layout = $objectManager->create(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, 'test1');

        $sut = $objectManager->create(BlockJsDependencies::class);
        $sut->setBlockTemplateDependency($block, 'foo.phtml');
        $this->assertSame(['foo.phtml' => BlockJsDependencies::DEFAULT_PRIORITY], $block->getData(BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY));
        $sut->setBlockTemplateDependencyWithPriority($block, 'foo.phtml', 10);
        $sut->setBlockTemplateDependencyWithPriority($block, 'bar.phtml', 20);
        $this->assertSame(['foo.phtml' => 10, 'bar.phtml' => 20], $block->getData(BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY));
    }
    public function testRegistersBlockNameDependencies(): void
    {
        $objectManager = ObjectManager::getInstance();
        $layout = $objectManager->create(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, 'test1');

        $sut = $objectManager->create(BlockJsDependencies::class);
        $sut->setBlockNameDependency($block, 'foo.block');
        $this->assertSame(['foo.block' => BlockJsDependencies::DEFAULT_PRIORITY], $block->getData(BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY));
        $sut->setBlockNameDependencyWithPriority($block, 'foo.block', 10);
        $sut->setBlockNameDependencyWithPriority($block, 'bar.block', 20);
        $this->assertSame(['foo.block' => 10, 'bar.block' => 20], $block->getData(BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY));
    }
}
