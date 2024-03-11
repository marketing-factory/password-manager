<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Mfc\PasswordManager\Services\ConfigurationService;
use Mfc\PasswordManager\Services\PasswordRolloutService;
use Mfc\PasswordManager\Services\UserStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class PasswordRotateCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class PasswordRotateCommand extends Command
{
    use ConfigDirectoryTrait;
    use DryRunTrait;

    /**
     * PasswordRotateCommand constructor.
     * @param ConfigurationService $configurationService
     * @param PasswordRolloutService $passwordRolloutService
     * @param UserStorage $userStorage
     */
    public function __construct(
        private ConfigurationService $configurationService,
        private PasswordRolloutService $passwordRolloutService,
        private UserStorage $userStorage
    ) {
        parent::__construct('users:rotate-passwords');
    }

    protected function configure()
    {
        $this
            ->setDescription('Rotate all passwords')
            ->configureConfigDirectory()
            ->configureDryRun();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($this->configurationService, $input);
        $this->checkDryRun($input);

        if ($this->dryRun) {
            $this->passwordRolloutService->enableDryRun();
        }

        $this->userStorage->changePasswordForAllUsers();
        $this->passwordRolloutService->rolloutUsers();

        return 0;
    }
}
