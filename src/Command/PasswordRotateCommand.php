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
     * @var ConfigurationService
     */
    private $configurationService;
    /**
     * @var PasswordRolloutService
     */
    private $passwordRolloutService;
    /**
     * @var UserStorage
     */
    private $userStorage;

    /**
     * PasswordRotateCommand constructor.
     * @param ConfigurationService $configurationService
     * @param PasswordRolloutService $passwordRolloutService
     * @param UserStorage $userStorage
     */
    public function __construct(
        ConfigurationService $configurationService,
        PasswordRolloutService $passwordRolloutService,
        UserStorage $userStorage
    ) {
        parent::__construct('users:rotate-passwords');

        $this->configurationService = $configurationService;
        $this->passwordRolloutService = $passwordRolloutService;
        $this->userStorage = $userStorage;
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
    }
}
