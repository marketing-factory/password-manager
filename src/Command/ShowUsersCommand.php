<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Mfc\PasswordManager\Model\User;
use Mfc\PasswordManager\Services\ConfigurationService;
use Mfc\PasswordManager\Services\UserStorage;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ShowUsersCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class ShowUsersCommand extends Command
{
    use ConfigDirectoryTrait;

    /**
     * @var ConfigurationService
     */
    private $configurationService;
    /**
     * @var UserStorage
     */
    private $userStorage;

    /**
     * ShowUsersCommand constructor.
     * @param ConfigurationService $configurationService
     * @param UserStorage $userStorage
     */
    public function __construct(ConfigurationService $configurationService, UserStorage $userStorage)
    {
        parent::__construct('users:show');

        $this->configurationService = $configurationService;
        $this->userStorage = $userStorage;
    }

    protected function configure()
    {
        $this
            ->setAliases(['users'])
            ->setDescription('Shows all configured users')
            ->configureConfigDirectory();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($this->configurationService, $input);

        $users = $this->userStorage->getUsers();

        $io = new SymfonyStyle($input, $output);
        $rows = [];
        /** @var User $user */
        foreach ($users as $user) {
            $rows[] = [
                $user->getUsername(),
                $user->getEmail(),
                $user->getFirstname(),
                $user->getLastname(),
                $user->isActive() ? 'yes' : 'no'
            ];
        }

        $io->table(
            [
                'Username',
                'Email',
                'First name',
                'Last name',
                'Active'
            ],
            $rows
        );

        return 0;
    }
}
