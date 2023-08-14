<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Cache\Type\Layout as LayoutCacheType;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\View\DesignInterface;
use Magento\PageCache\Model\Cache\Type as PageCacheType;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\TestCase\AbstractController;
use Magento\TestFramework\View\Layout;
use Magento\Theme\Model\Theme\Registration;
use Psr\Log\LoggerInterface;

// phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps

/**
 * @magentoAppArea frontend
 * @magentoAppIsolation enabled
 * @magentoComponentsDir ../../../../vendor/hyva-themes/magento2-theme-module/tests/integration/_files/design
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
class LayoutUpdateHandlesTest extends AbstractController
{
    /**
     * @before
     */
    public function cleanViewCache()
    {
        /**
         * Ensure the layout update object is populated with layout handles during dispatch
         */
        ObjectManager::getInstance()->get(CacheInterface::class)
                                    ->clean([LayoutCacheType::CACHE_TAG]);
        ObjectManager::getInstance()->get(PageCacheType::class)
                                    ->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, [PageCacheType::CACHE_TAG]);
    }

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
            'Layout handles should be unchanged if not hyva theme'
        );
    }

    /** @test */
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

    /**
     * @test
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoConfigFixture current_store customer/captcha/enable 0
     */
    public function loads_layout_handles_added_with_update_xml_directive()
    {
        $this->givenCurrentTheme('Hyva/default');
        $this->_objectManager->get(Session::class)->loginById(/* fixture customer id */ 1);
        $this->dispatch('customer/account/index');
        /** @var Layout $layout */
        $layout = $this->_objectManager->get(Layout::class);
        $xml = $layout->getUpdate()->getFileLayoutUpdatesXml()->asXml();
        $this->assertTrue(
            strpos($xml, '<update handle="hyva_customer_account"/>') !== false,
            'Layout handles added with \'<update handle="..."/>\' should be duplicated with hyva prefix'
        );
    }

    /** @test */
    public function block_loaded_from_hyva_prefix_layout()
    {
        $this->givenCurrentTheme('Hyva/integration-test');
        $this->dispatch('/');
        /** @var Layout $layout */
        $layout = $this->_objectManager->get(Layout::class);
        $this->assertNotFalse(
            $layout->getBlock('hyva.custom.block'),
            'Custom block from hyva_* layout should be loaded in hyva theme'
        );
    }

    private function givenCurrentTheme(string $themePath): void
    {
        /** @var Registration $registration */
        $registration = Bootstrap::getObjectManager()->get(Registration::class);
        $registration->register();

        /** @var DesignInterface $design */
        $design = Bootstrap::getObjectManager()->get(DesignInterface::class);
        $design->setDesignTheme($themePath);
    }
}
