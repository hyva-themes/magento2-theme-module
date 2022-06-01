<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\Console\Command;

use Hyva\Theme\Model\HyvaModulesConfig;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class HyvaConfigGenerate extends Command
{
    /**
     * @var HyvaModulesConfig
     */
    private $hyvaModulesConfig;

    public function __construct(HyvaModulesConfig $hyvaModulesConfig)
    {
        parent::__construct();

        $this->hyvaModulesConfig = $hyvaModulesConfig;
    }

    protected function configure()
    {
        $this->setName('hyva:config:generate');
        $this->setDescription('Generate Hyvä Themes app/etc/hyva-themes.json configuration file');
        $this->addOption('info', null, InputOption::VALUE_NONE, 'More information');
        $this->addOption('dump', 'd', InputOption::VALUE_NONE, 'Dump to STDOUT instead of writing to the file');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'No effect, exists for backward compatibility');
    }

    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        if ($input->getOption('info')) {
            return $this->help($output);
        }

        if ($input->getOption('dump')) {
            $output->write($this->hyvaModulesConfig->getJson());
        } else {
            $fullPath = $this->hyvaModulesConfig->generateFile();
            $output->writeln("<info>Hyvä Themes - Configuration file generated at \"$fullPath\".</info>");
        }
        return Cli::RETURN_SUCCESS;
    }

    private function help(OutputInterface $output): int
    {
        $url = 'https://docs.hyva.io/hyva-themes/compatibility-modules/tailwind-config-merging.html';
        $output->writeln("<info>Go check our documentation at $url</info>");
        return Cli::RETURN_SUCCESS;
    }
}
