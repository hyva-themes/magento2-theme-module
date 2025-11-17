<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Setup;

use Composer\InstalledVersions as Composer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class RecurringData implements InstallDataInterface
{
    /**
     * Unset or set the parent theme id for the Hyvä default themes after upgrading to 1.4.0 or newer
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        foreach ([
                 'Hyva/default'     => 'hyva-themes/magento2-default-theme',
                 'Hyva/default-csp' => 'hyva-themes/magento2-default-theme-csp',
             ] as $themeCode => $packageName) {
            $version = $this->getInstalledVersion($packageName);
            if (null !== $version && version_compare('1.4.0', $version, '<=')) {
                // Ensure the default theme parent_id is NULL for default themes >= 1.4.0
                $this->ensureThemeParentIdIsNull($setup, $themeCode);
            }
            // Do not set the parent theme ID to Hyva/reset since we want to allow manual migrations to the base layout reset
        }
        $setup->endSetup();
    }

    private function getInstalledVersion(string $packageName): ?string
    {
        // In case a package is composer-replaced, isInstalled with return true, but getVersion will return null
        return Composer::isInstalled($packageName)
            ? Composer::getVersion($packageName)
            : null;
    }

    private function ensureThemeParentIdIsNull(ModuleDataSetupInterface $setup, string $themeCode): void
    {
        $parentId = $this->getParentId($setup, $themeCode);
        if ($parentId !== null) {
            $adapter = $setup->getConnection();
            $adapter->update($setup->getTable('theme'), ['parent_id' => null], $adapter->quoteInto('code = ?', $themeCode));
        }
    }

    private function getParentId(ModuleDataSetupInterface $setup, string $themeCode)
    {
        $select = sprintf('SELECT parent_id FROM %s WHERE code = :code', $setup->getTable('theme'));

        return $setup->getConnection()->fetchOne($select, ['code' => $themeCode]);
    }
}
