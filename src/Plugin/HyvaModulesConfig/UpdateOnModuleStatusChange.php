<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\HyvaModulesConfig;

use Hyva\Theme\Model\HyvaModulesConfig;
use Magento\Deploy\Console\Command\App\ConfigImport\Processor as ConfigImportProcessor;
use Magento\Framework\App\DeploymentConfig\Writer as DeploymentConfigWriter;

/** @SuppressWarnings(PHPMD.UnusedFormalParameter) */
class UpdateOnModuleStatusChange
{
    /**
     * @var HyvaModulesConfig
     */
    private $hyvaModulesConfig;

    /**
     * Flag to avoid generating the config twice on older instances.
     *
     * @var bool
     */
    private $hyvaModulesConfigIsGenerated = false;

    public function __construct(HyvaModulesConfig $hyvaModulesConfig)
    {
        $this->hyvaModulesConfig = $hyvaModulesConfig;
    }

    /**
     * Trigger hyva-themes.json generation any time app/etc/config.php or env.php is written.
     *
     * Most notably, this happens during setup:install, setup:upgrade, module:enable and module:disable.
     *
     * @param DeploymentConfigWriter $subject
     * @param null $result
     * @return null
     */
    public function afterSaveConfig(DeploymentConfigWriter $subject, $result)
    {
        if (! $this->hyvaModulesConfigIsGenerated) {
            $this->hyvaModulesConfigIsGenerated = true;
            $this->hyvaModulesConfig->generateFile();
        }
        return $result;
    }

    /**
     * Trigger hyva-themes.json generation app/etc/env.php or app/etc/config.php are imported.
     *
     * In Magento 2.4.7 it is no longer possible to plug into the DeploymentConfigWriter during
     * setup:upgrade. Instead, we also intercept the ConfigImportProcessor.
     *
     * @param ConfigImportProcessor $subject
     * @param null $result
     * @return null
     */
    public function afterExecute(ConfigImportProcessor $subject, $result)
    {
        if (! $this->hyvaModulesConfigIsGenerated) {
            $this->hyvaModulesConfigIsGenerated = true;
            $this->hyvaModulesConfig->generateFile();
        }
        return $result;
    }
}
