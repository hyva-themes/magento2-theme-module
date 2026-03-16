<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Setup;

use Hyva\Theme\Model\HyvaModulesConfig;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Recurring implements InstallSchemaInterface
{
    /**
     * @var HyvaModulesConfig
     */
    private $hyvaModulesConfig;

    public function __construct(HyvaModulesConfig $hyvaModulesConfig)
    {
        $this->hyvaModulesConfig = $hyvaModulesConfig;
    }

    /**
     * Regenerate app/etc/hyva-themes.json during setup:install and setup:upgrade.
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->hyvaModulesConfig->generateFile();
    }
}
