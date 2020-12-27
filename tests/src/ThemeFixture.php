<?php
declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Framework\View\DesignInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Model\Theme\Registration;

class ThemeFixture
{

    /**
     * Re-register themes from the magentoComponentsDir fixture
     */
    public static function registerTestThemes(): void
    {
        /** @var Registration $registration */
        $registration = Bootstrap::getObjectManager()->get(Registration::class);
        $registration->register();
    }

    public static function setCurrentTheme(string $themePath): void
    {
        /** @var DesignInterface $design */
        $design = Bootstrap::getObjectManager()->get(DesignInterface::class);
        $design->setDesignTheme($themePath);
    }
}
