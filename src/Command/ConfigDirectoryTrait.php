<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Mfc\PasswordManager\Services\ConfigurationService;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait ConfigDirectoryTrait
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
trait ConfigDirectoryTrait
{
    protected function configureConfigDirectory(): self
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to the configuration file (defaults to /etc/pwmgr/config.yaml)'
        );

        return $this;
    }

    protected function loadConfiguration(ConfigurationService $configurationService, InputInterface $input)
    {
        $filename = $input->getOption('config') ?? '/etc/pwmgr/config.yaml';
        $configurationService->loadConfiguration($filename);
    }
}
