<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Model\PageJsDependencyRegistry;
use Hyva\Theme\ViewModel\BlockJsDependencies;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;
use function array_values as values;

class RegisterPageJsDependenciesTest extends TestCase
{
    public function testUncachedBlockDependencies(): void
    {
        $objectManager = ObjectManager::getInstance();
        $cache = $objectManager->create(CacheInterface::class);
        $layout = $objectManager->create(LayoutInterface::class);
        $dependencyRegistry = $objectManager->create(PageJsDependencyRegistry::class, ['layout' => $layout]);

        $block1Dependencies = [
            BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY    => [
                'foo.block' => '10',
                'bar.block' => '20',
            ],
            BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY => [
                'foo.phtml' => '10',
                'bar.phtml' => '10',
            ],
        ];
        $block1 = $layout->createBlock(Template::class, 'test1', ['data' => $block1Dependencies]);

        $block2Dependencies = [
            BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY    => [
                'buz.block' => '20',
                'bar.block' => '20',
            ],
            BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY => [
                'foo.phtml' => '10',
                'buz.phtml' => '30',
            ],
        ];
        $block2 = $layout->createBlock(Template::class, 'test2', ['data' => $block2Dependencies]);

        $fooBlock = $layout->createBlock(Template::class, 'foo.block');
        $buzBlock = $layout->createBlock(Template::class, 'buz.block');

        $block1->setChild('test2', $block2);
        $cache->remove($block1->getCacheKey());

        $sut = $objectManager->create(RegisterPageJsDependencies::class, [
            'jsDependencyRegistry' => $dependencyRegistry,
            'layout'               => $layout,
            'cache'                => $cache,
        ]);
        $sut->execute($objectManager->create(Event::class, [
            'data' => [
                'block'     => $block1,
                'transport' => $objectManager->create(DataObject::class),
            ],
        ]));

        $deps = $dependencyRegistry->getUnsortedJsDependencies();
        $this->assertSame([
            [
                'block'    => $fooBlock,
                'priority' => '10',
            ],
            [
                'block'    => $buzBlock,
                'priority' => '20',
            ],
            [
                'template' => 'foo.phtml',
                'priority' => '10',
            ],
            [
                'template' => 'bar.phtml',
                'priority' => '10',
            ],
            [
                'template' => 'buz.phtml',
                'priority' => '30',
            ],
        ], values($deps));
    }
}
