<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Mfc\PasswordManager\Services\ConfigurationService;
use Mfc\PasswordManager\Services\UserStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ChangeUserPasswordCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class ChangeUserPasswordCommand extends Command
{
    use ConfigDirectoryTrait;
    use DryRunTrait;

    /**
     * @var ConfigurationService
     */
    private $configurationService;
    /**
     * @var UserStorage
     */
    private $userStorage;

    /**
     * ChangeUserPasswordCommand constructor.
     * @param ConfigurationService $configurationService
     * @param UserStorage $userStorage
     */
    public function __construct(ConfigurationService $configurationService, UserStorage $userStorage)
    {
        parent::__construct('users:change-password');

        $this->configurationService = $configurationService;
        $this->userStorage = $userStorage;
    }

    protected function configure()
    {
        $this
            ->setDescription('Change password for user')
            ->configureConfigDirectory()
            ->configureDryRun()
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Username of the user whose password should be changed'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($this->configurationService, $input);
        $this->checkDryRun($input);

        $username = $input->getArgument('username');

        if ($this->dryRun) {
            $this->userStorage->enableDryRun();
        }

        $this->userStorage->changePasswordForUser($username);

        return 0;
    }
}
