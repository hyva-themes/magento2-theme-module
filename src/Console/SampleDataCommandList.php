<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\Theme\Console;

use Hyva\Theme\Console\Command\SampleData\DeployCommand;
use Hyva\Theme\Console\Command\SampleData\RemoveCommand;
use Magento\Framework\Console\CommandListInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Registers the Hyvä sample data CLI commands via the CommandLocator.
 *
 * Using the CommandLocator (see cli_commands.php) instead of the
 * di.xml Magento\Framework\Console\CommandListInterface argument means the
 * commands are available even when Magento is not fully installed yet, which
 * matches Magento core's own sampledata:deploy registration.
 */
class SampleDataCommandList implements CommandListInterface
{
    private ObjectManagerInterface $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function getCommands(): array
    {
        return [
            $this->objectManager->get(DeployCommand::class),
            $this->objectManager->get(RemoveCommand::class),
        ];
    }
}
