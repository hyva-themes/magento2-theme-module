<?php
declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\TestFramework\View\Layout;

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 */
class LayoutUpdateHandlesTest extends AbstractController
{
    /** @test */
    public function unchanged_if_not_hyva_theme()
    {
        $this->givenCurrentTheme('Magento/luma');
        $this->dispatch('/');
        /** @var Layout $layout */
        $layout = $this->_objectManager->get(Layout::class);
        $this->assertEqualsCanonicalizing(
            [
                'cms_index_index',
                'cms_index_index_id_home',
                'cms_page_view',
                'default',
            ],
            $layout->getUpdate()->getHandles(),
            'Layout handles should be unchanged'
        );
    }    /** @test */

    public function added_with_hyva_prefix_if_hyva_theme()
    {
        $this->givenCurrentTheme('Hyva/default');
        $this->dispatch('/');
        /** @var Layout $layout */
        $layout = $this->_objectManager->get(Layout::class);
        $this->assertEqualsCanonicalizing(
            [
                'cms_index_index',
                'cms_index_index_id_home',
                'cms_page_view',
                'default',
                'hyva_cms_index_index',
                'hyva_cms_index_index_id_home',
                'hyva_cms_page_view',
                'hyva_default',
            ],
            $layout->getUpdate()->getHandles(),
            'All layout handles should be duplicated with hyva prefix'
        );
    }

    private function givenCurrentTheme(string $themePath): void
    {
        /** @var DesignInterface $design */
        $design = Bootstrap::getObjectManager()->get(DesignInterface::class);
        $design->setDesignTheme($themePath);
    }
}
