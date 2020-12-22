<?php
declare(strict_types=1);

namespace Hyva\Theme;

use Hyva\Theme\Model\InvalidViewModelClass;
use Hyva\Theme\Model\ViewModelRegistry;
use Hyva\Theme\ViewModel\StoreConfig;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Session;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea frontend
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
}
