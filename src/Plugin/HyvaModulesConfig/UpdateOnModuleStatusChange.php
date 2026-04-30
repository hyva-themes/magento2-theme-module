<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Plugin\HyvaModulesConfig;

use Hyva\Theme\Model\HyvaModulesConfig;
use Magento\Framework\Module\Status;

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
     * Regenerate hyva-themes.json after module:enable or module:disable.
     *
     * @param Status $subject
     * @param void $result
     * @param bool $isEnabled
     * @param string[] $modules
     * @return void
     */
    public function afterSetIsEnabled(Status $subject, $result, $isEnabled, $modules)
    {
        $this->hyvaModulesConfig->generateFile();
        return $result;
    }
}
