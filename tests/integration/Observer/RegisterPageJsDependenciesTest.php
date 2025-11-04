<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Observer;

use Hyva\Theme\Model\PageJsDependencyRegistry;
use Hyva\Theme\Service\CurrentTheme;
use Hyva\Theme\ViewModel\BlockJsDependencies;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer as Event;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

use function array_values as values;

/**
 * @covers \Hyva\Theme\Observer\RegisterPageJsDependencies
 */
class RegisterPageJsDependenciesTest extends TestCase
{
    /**
     * @before
     */
    public function emulateHyvaTheme(): void
    {
        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();
        $objectManager->addSharedInstance(new class extends CurrentTheme
        {
            public function __construct()
            {
                // Leave empty and don't call parent constructor on purpose
            }

            public function isHyva(): bool
            {
                return true;
            }

        }, CurrentTheme::class);
    }

    /**
     * @after
     */
    public function stopHyvaThemeEmulation(): void
    {

        /** @var ObjectManager $objectManager */
        $objectManager = ObjectManager::getInstance();
        $objectManager->removeSharedInstance(CurrentTheme::class);
    }

    public function testDoesNotCollectChildDependenciesFromUncachedBlocks(): void
    {
        $objectManager = ObjectManager::getInstance();
        $cache = $objectManager->create(CacheInterface::class);
        $layout = $objectManager->create(LayoutInterface::class);
        $dependencyRegistry = $objectManager->create(PageJsDependencyRegistry::class, ['layout' => $layout]);

        $uncachedBlockDependencies = [
            BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY    => [
                'foo.block' => '10',
                'bar.block' => '20',
            ],
            BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY => [
                'foo.phtml' => '10',
                'bar.phtml' => '10',
            ],
        ];
        $uncachedBlock = $layout->createBlock(Template::class, 'test1', ['data' => $uncachedBlockDependencies]);
        $uncachedBlock->setData('cache_lifetime', null);

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

        $layout->createBlock(Template::class, 'foo.block');
        $layout->createBlock(Template::class, 'buz.block');

        $uncachedBlock->setChild('test2', $block2);
        $cache->remove($uncachedBlock->getCacheKey());

        $sut = $objectManager->create(RegisterPageJsDependencies::class, [
            'jsDependencyRegistry' => $dependencyRegistry,
            'layout'               => $layout,
            'cache'                => $cache,
        ]);
        $sut->execute($objectManager->create(Event::class, [
            'data' => [
                'block'     => $uncachedBlock,
                'transport' => $objectManager->create(DataObject::class),
            ],
        ]));

        $deps = $dependencyRegistry->getUnsortedJsDependencies();
        $this->assertSame([
            [
                'block'    => $layout->getBlock('foo.block'),
                'priority' => '10',
            ],
            [
                'template' => 'foo.phtml',
                'priority' => '10',
            ],
            [
                'template' => 'bar.phtml',
                'priority' => '10',
            ]
        ], values($deps));
    }

    public function testCollectChildDependenciesFromCachedBlocks(): void
    {
        $objectManager = ObjectManager::getInstance();
        $cache = $objectManager->create(CacheInterface::class);
        $layout = $objectManager->create(LayoutInterface::class);
        $dependencyRegistry = $objectManager->create(PageJsDependencyRegistry::class, ['layout' => $layout]);

        $cachedBlockDependencies = [
            BlockJsDependencies::HYVA_JS_BLOCK_DEPENDENCIES_KEY    => [
                'foo.block' => '10',
                'bar.block' => '20',
            ],
            BlockJsDependencies::HYVA_JS_TEMPLATE_DEPENDENCIES_KEY => [
                'foo.phtml' => '10',
                'bar.phtml' => '10',
            ],
        ];
        $cachedBlock = $layout->createBlock(Template::class, 'test1', ['data' => $cachedBlockDependencies]);
        $cachedBlock->setData('cache_lifetime', 5000);

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

        $layout->createBlock(Template::class, 'foo.block');
        $layout->createBlock(Template::class, 'buz.block');

        $cachedBlock->setChild('test2', $block2);
        $cache->remove($cachedBlock->getCacheKey());

        $sut = $objectManager->create(RegisterPageJsDependencies::class, [
            'jsDependencyRegistry' => $dependencyRegistry,
            'layout'               => $layout,
            'cache'                => $cache,
        ]);
        $sut->execute($objectManager->create(Event::class, [
            'data' => [
                'block'     => $cachedBlock,
                'transport' => $objectManager->create(DataObject::class),
            ],
        ]));

        $deps = $dependencyRegistry->getUnsortedJsDependencies();
        $this->assertSame([
            [
                'block'    => $layout->getBlock('foo.block'),
                'priority' => '10',
            ],
            [
                'block'    => $layout->getBlock('buz.block'),
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
            ]
        ], values($deps));
    }
}
