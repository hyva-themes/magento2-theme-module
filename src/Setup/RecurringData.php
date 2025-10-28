<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
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
     * Unset or set the parent theme id for the Hyvä default themes depending on the installed versions.
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        foreach ([
                     'Hyva/default'     => 'hyva-themes/magento2-default-theme',
                     'Hyva/default-csp' => 'hyva-themes/magento2-default-theme-csp',
                 ] as $themeCode => $packageName) {
            if (Composer::isInstalled($packageName)) {
                $this->ensureThemeHasTheCorrectParentId($setup, $themeCode, $packageName);
            }
        }
        $setup->endSetup();
    }

    private function ensureThemeHasTheCorrectParentId(ModuleDataSetupInterface $setup, string $themeCode, string $packageName): void
    {
        $themeVersion = Composer::getVersion($packageName);
        if (version_compare('1.3.17.0', $themeVersion, '<')) {
            $this->ensureThemeParentIdIsNull($setup, $themeCode);
        } else {
            $this->ensureParentIdIsSetToHyvaReset($setup, $themeCode);
        }
    }

    /**
     * Ensure the default theme parent_id is NULL for default themes >= 1.3.18
     */
    private function ensureThemeParentIdIsNull(ModuleDataSetupInterface $setup, string $themeCode): void
    {
        $parentId = $this->getParentId($setup, $themeCode);
        if ($parentId !== null) {
            $adapter = $setup->getConnection();
            $adapter->update($adapter->getTableName('theme'), ['parent_id' => null], $adapter->quoteInto('code = ?', $themeCode));
        }
    }

    private function getParentId(ModuleDataSetupInterface $setup, string $themeCode)
    {
        $adapter = $setup->getConnection();
        $select = sprintf('SELECT parent_id FROM %s WHERE code = :code', $adapter->getTableName('theme'));

        return $adapter->fetchOne($select, ['code' => $themeCode]);
    }

    /**
     * Ensure parent_id is set to Hyva/reset id for default themes < 1.3.18
     */
    private function ensureParentIdIsSetToHyvaReset(ModuleDataSetupInterface $setup, string $themeCode): void
    {
        $resetThemeId = $this->getThemeId($setup, 'Hyva/reset');
        if ($resetThemeId) {
            $adapter = $setup->getConnection();
            $adapter->update($adapter->getTableName('theme'), ['parent_id' => $resetThemeId], $adapter->quoteInto('code = ?', $themeCode));
        }
    }

    private function getThemeId(ModuleDataSetupInterface $setup, string $themeCode)
    {
        $adapter = $setup->getConnection();
        $select = sprintf('SELECT theme_id FROM %s WHERE code = :code', $adapter->getTableName('theme'));

        return $adapter->fetchOne($select, ['code' => $themeCode]);
    }
}
