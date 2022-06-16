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
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;
use Magento\Framework\Config\File\ConfigFilePool;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
class UpdateOnModuleStatusChange
{
    /**
     * @var HyvaModulesConfig
     */
    private $hyvaModulesConfig;

    private DeploymentConfig $deploymentConfig;

    public function __construct(HyvaModulesConfig $hyvaModulesConfig, DeploymentConfig $deploymentConfig)
    {
        $this->hyvaModulesConfig = $hyvaModulesConfig;
        $this->deploymentConfig  = $deploymentConfig;
    }

    /**
     * Trigger hyva-themes.json generation any time app/etc/config.php or env.php is written
     *
     * Most notably, this happens during setup:install, setup:upgrade, module:enable and module:disable.
     * The generation has to be skipped during the installation because it is incompatible with the required app state.
     *
     * @param DeploymentConfigWriter $subject
     * @param null $result
     * @return null
     */
    public function afterSaveConfig(DeploymentConfigWriter $subject, $result, array $data)
    {
        if (! $this->isInstallation()) {
            $this->generateHyvaConfig();
        }
        return $result;
    }

    private function isInstallation(): bool
    {
        return ! $this->deploymentConfig->isAvailable();
    }

    private function generateHyvaConfig()
    {
        $this->hyvaModulesConfig->generateFile();
    }
}
