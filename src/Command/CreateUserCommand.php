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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * Class CreateUserCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class CreateUserCommand extends Command
{
    use ConfigDirectoryTrait;

    /**
     * CreateUserCommand constructor.
     * @param ConfigurationService $configurationService
     * @param UserStorage $userStorage
     */
    public function __construct(private ConfigurationService $configurationService, private UserStorage $userStorage)
    {
        parent::__construct('users:create');
    }

    protected function configure()
    {
        $this
            ->setAliases(['create'])
            ->setDescription('Create a new user')
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the new user')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email address of the new user')
            ->addArgument('firstname', InputArgument::OPTIONAL, 'The first name of the new user')
            ->addArgument('lastname', InputArgument::OPTIONAL, 'The last name of the new user')
            ->configureConfigDirectory();
    }

    /**
     * @return string
     */
    public function validateEmailAddress(mixed $input): string
    {
        $input = trim((string)$input);

        $validator = Validation::createValidator();

        $errors = $validator->validate(
            $input,
            [
                new Assert\Email(),
                new Assert\NotBlank()
            ]
        );

        if (0 !== count($errors)) {
            throw new \RuntimeException('You have to specify a valid email address');
        }

        return $input;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->loadConfiguration($this->configurationService, $input);

        $io = new SymfonyStyle($input, $output);

        if (empty($username = $input->getArgument('username'))) {
            $username = $io->ask('Please enter the username of the new user', null, function ($input) {
                $input = trim((string)$input);
                if (empty($input)) {
                    throw new \RuntimeException('You have to specify a username');
                }

                return $input;
            });
        }

        $user = $this->userStorage->loadUser($username);
        if (!is_null($user)) {
            throw new \RuntimeException("User \"{$username}\" already exists. Pick another name");
        }

        if (empty($email = $input->getArgument('email'))) {
            $email = $io->ask('Please enter the email address of the new user', null, $this->validateEmailAddress(...));
        } else {
            $this->validateEmailAddress($email);
        }

        if (empty($firstname = $input->getArgument('firstname'))) {
            $firstname = $io->ask('Please enter the first name of the new user', null, function ($input) {
                $input = trim((string)$input);
                if (empty($input)) {
                    throw new \RuntimeException('You have to specify a first name');
                }

                return $input;
            });
        }

        if (empty($lastname = $input->getArgument('lastname'))) {
            $lastname = $io->ask('Please enter the last name of the new user', null, function ($input) {
                $input = trim((string)$input);
                if (empty($input)) {
                    throw new \RuntimeException('You have to specify a last name');
                }

                return $input;
            });
        }

        $this->userStorage->createUser($username, $email, $firstname, $lastname);

        return 0;
    }
}
