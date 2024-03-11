<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Mfc\PasswordManager\Services\ConfigurationService;
use Mfc\PasswordManager\Services\PasswordRolloutService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PasswordRolloutCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class PasswordRolloutCommand extends Command
{
    use ConfigDirectoryTrait;
    use DryRunTrait;

    /**
     * PasswordRolloutCommand constructor.
     * @param ConfigurationService $configurationService
     * @param PasswordRolloutService $passwordRolloutService
     */
    public function __construct(
        private ConfigurationService $configurationService,
        private PasswordRolloutService $passwordRolloutService
    ) {
        parent::__construct('users:rollout');
    }

    protected function configure()
    {
        $this
            ->setAliases(['rollout'])
            ->setDescription('Rollout users and passwords')
            ->configureConfigDirectory()
            ->configureDryRun()
            ->addOption('user', 'u', InputOption::VALUE_REQUIRED, 'Only roll out a single user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($this->configurationService, $input);
        $this->checkDryRun($input);

        if ($this->dryRun) {
            $this->passwordRolloutService->enableDryRun();
        }

        if ($user = $input->getOption('user')) {
            $this->passwordRolloutService->rolloutUser($user);
        } else {
            $this->passwordRolloutService->rolloutUsers();
        }

        return 0;
    }
}
