<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Model\InvalidViewModelClass;
use Hyva\Theme\Model\ViewModelCacheTags;
use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\Theme\StubViewModels;
use Hyva\Theme\ViewModel\StoreConfig;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Session;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

use function array_merge as merge;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class ViewModelRegistryTest extends TestCase
{
    /**
     * @var ViewModelRegistry
     */
    private $viewModelRegistry;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->viewModelRegistry = $this->objectManager->get(ViewModelRegistry::class);
    }

    /**
     * @test
     */
    public function returns_view_model_by_fully_qualified_classname()
    {
        $this->assertInstanceOf(StoreConfig::class, $this->viewModelRegistry->require(StoreConfig::class));
    }

    /**
     * @test
     */
    public function throws_exception_if_class_does_not_exist()
    {
        $this->expectException(InvalidViewModelClass::class);
        $this->expectExceptionMessage(
            "Class That\Does\Not\Exist not found."
        );
        $this->viewModelRegistry->require('That\\Does\\Not\\Exist');
    }

    /**
     * @test
     */
    public function throws_exception_if_class_is_not_a_view_model()
    {
        $this->expectException(InvalidViewModelClass::class);
        $this->expectExceptionMessage(
            "Class Magento\Framework\Session\Generic is not a view model.\nOnly classes that implement "
            . "Magento\Framework\View\Element\Block\ArgumentInterface can be used as view model"
        );
        $this->viewModelRegistry->require(Session\Generic::class);
    }

    /**
     * @test
     * @dataProvider viewModelIdentityDataProvider
     */
    public function records_identities_of_required_viewmodels(array $viewModelClasses, array $expected): void
    {
        foreach ($viewModelClasses as $viewModelClass) {
            $this->viewModelRegistry->require($viewModelClass);
        }
        /** @var ViewModelCacheTags $vieModelIdentities */
        $vieModelIdentities = $this->objectManager->get(ViewModelCacheTags::class);
        $this->assertSame($expected, $vieModelIdentities->get());
    }

    public function viewModelIdentityDataProvider(): array
    {
        return [
            'no-view-models' => [[], []],
            'non-identity' => [[StubViewModels\NoIdentity::class], []],
            'empty-identity' => [[StubViewModels\EmptyIdentities::class], []],
            'simgle-identity' => [[StubViewModels\SingleIdentity::class], StubViewModels\SingleIdentity::TAGS],
            'multipe-identity' => [[StubViewModels\MultipleIdentities::class], StubViewModels\MultipleIdentities::TAGS],
            'multipe-view-models' => [
                [StubViewModels\SingleIdentity::class, StubViewModels\MultipleIdentities::class],
                merge(StubViewModels\SingleIdentity::TAGS, StubViewModels\MultipleIdentities::TAGS)
            ],
            'duplicate' => [
                [StubViewModels\MultipleIdentities::class, StubViewModels\MultipleIdentities::class],
                StubViewModels\MultipleIdentities::TAGS
            ],
        ];
    }
}
