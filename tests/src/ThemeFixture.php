<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme;

use Magento\Framework\App\ResourceConnection;
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

    public static function setHyvaDefaultAsCurrentTheme(): void
    {
        self::setCurrentTheme(self::getInstalledHyvaDefaultThemeCode());
    }

    public static function getInstalledHyvaDefaultThemeCode(): string
    {
        $db = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getConnection();
        $themeTable = Bootstrap::getObjectManager()->get(ResourceConnection::class)->getTableName('theme');
        return $db->fetchOne(sprintf('SELECT code FROM %s WHERE code in ("Hyva/default", "Hyva/default-csp")', $themeTable));
    }
}
