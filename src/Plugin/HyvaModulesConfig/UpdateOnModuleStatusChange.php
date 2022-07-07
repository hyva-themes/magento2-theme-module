<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\HyvaModulesConfig;

use Hyva\Theme\Model\HyvaModulesConfig;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
class UpdateOnModuleStatusChange
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
     * Trigger hyva-themes.json generation any time app/etc/config.php or env.php is written
     *
     * Most notably, this happens during setup:install, setup:upgrade, module:enable and module:disable.
     *
     * @param DeploymentConfigWriter $subject
     * @param null $result
     * @return null
     */
    public function afterSaveConfig(DeploymentConfigWriter $subject, $result)
    {
        $this->hyvaModulesConfig->generateFile();
        return $result;
    }
}
