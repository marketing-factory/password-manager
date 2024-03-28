<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Services;

use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Mfc\PasswordManager\Model\User;
use Mfc\PasswordManager\Security\Encoder\Argon2iPasswordEncoder;
use Mfc\PasswordManager\Security\Encoder\BCryptPasswordEncoder;
use Mfc\PasswordManager\Security\Encoder\CryptMd5PasswordEncoder;
use Mfc\PasswordManager\Security\Encoder\Md5PasswordEncoder;
use Mfc\PasswordManager\Security\Encoder\PasswordEncoderInterface;
use Mfc\PasswordManager\Security\Encoder\Sha1PasswordEncoder;
use Mfc\PasswordManager\Services\Mail\MailerFactory;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Mime\Address;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class UserStorage
 * @package Mfc\PasswordManager\Services
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class UserStorage
{
    private $encoderClasses = [
        Argon2iPasswordEncoder::class,
        BCryptPasswordEncoder::class,
        CryptMd5PasswordEncoder::class,
        Md5PasswordEncoder::class,
        Sha1PasswordEncoder::class
    ];

    /**
     * @var bool
     */
    private $dryRun = false;

    /**
     * UserStorage constructor.
     */
    public function __construct(
        private ConfigurationService $configurationService,
        private readonly MailerFactory $mailerFactory,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    public function getUsers(): array
    {
        $finder = new Finder();
        $userFiles = $finder->in($this->configurationService['[users_path]'])
            ->name('*.yaml');

        $users = [];
        /** @var SplFileInfo $userFile */
        foreach ($userFiles as $userFile) {
            $yaml = $userFile->getContents();
            $user = $this->serializer->deserialize($yaml, User::class, 'yaml');

            $users[] = $user;
        }

        return $users;
    }

    public function loadUser(string $username): ?User
    {
        $userFile = $this->getUserFilename($username);

        if (!file_exists($userFile) || !is_readable($userFile)) {
            return null;
        }

        return $this->loadUserFromFile($userFile);
    }

    public function createUser(string $username, string $email, string $firstname, string $lastname): User
    {
        $user = new User();
        $user->setUsername($username)
            ->setEmail($email)
            ->setFirstname($firstname)
            ->setLastname($lastname)
            ->setEmail($email)
            ->setActive(true);

        $this->saveUserToFile($user, $this->getUserFilename($username));

        $user = $this->changePasswordForUser($user);

        return $user;
    }

    private function generateNewPassword(): string
    {
        $generator = new ComputerPasswordGenerator();

        $generator
            ->setUppercase()
            ->setLowercase()
            ->setNumbers()
            ->setSymbols(true)
            ->setLength(32);

        return $generator->generatePassword();
    }

    private function loadUserFromFile(string $filename): User
    {
        $yaml = file_get_contents($filename);

        /** @var User $user */
        $user = $this->serializer->deserialize($yaml, User::class, 'yaml');

        return $user;
    }

    private function saveUserToFile(User $user, string $filename): void
    {
        $yaml = $this->serializer->serialize($user, 'yaml', [
            'yaml_inline' => 2,
            'yaml_flags' => Yaml::DUMP_OBJECT_AS_MAP
        ]);

        if ($this->dryRun) {
            $this->logger->info(
                'Would save user {username} into {filename}',
                [
                    'username' => $user->getUsername(),
                    'filename' => $filename
                ]
            );
        } else {
            $this->logger->info(
                'Saving user {username} into {filename}',
                [
                    'username' => $user->getUsername(),
                    'filename' => $filename
                ]
            );
            file_put_contents($filename, $yaml);
        }
    }

    /**
     * @param string|User $user
     * @return User
     */
    public function changePasswordForUser($user): User
    {
        if (is_string($user)) {
            $userFilename = $this->getUserFilename($user);
            $user = $this->loadUserFromFile($userFilename);
        } else {
            $userFilename = $this->getUserFilename($user->getUsername());
        }

        $plainPassword = $this->generateNewPassword();

        $passwords = [];
        foreach ($this->encoderClasses as $encoderClass) {
            /** @var PasswordEncoderInterface $encoder */
            $encoder = new $encoderClass();

            $algorithm = $encoder->getAlgorithmKey();
            $hash = $encoder->encodePassword($plainPassword);
            $passwords[$algorithm] = $hash;

            $this->logger->debug(
                'Encode password for user {username} with algorithm {algorithm} into: {hash}',
                [
                    'algorithm' => $algorithm,
                    'username' => $user->getUsername(),
                    'hash' => $hash
                ]
            );
        }

        $user->setHashedPasswords($passwords);

        $this->saveUserToFile($user, $userFilename);

        $this->sendPlainPasswordToUser($user, $plainPassword);

        return $user;
    }

    private function sendPlainPasswordToUser(User $user, string $plainPassword): void
    {
        $username = $user->getUsername();

        $email = (new NotificationEmail())
            ->from(
                new Address('technik@marketing-factory.de', 'MFC Technik')
            )
            ->to(
                new Address($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname())
            )
            ->subject('New password for web applications')
            ->markdown(<<<EOF
                Hi {$user->getFirstname()},
                
                the password for your account "$username" has been changed.
                
                The new password is: `$plainPassword`
                
                EOF
            )
            ->importance(NotificationEmail::IMPORTANCE_HIGH);

        $mailer = $this->mailerFactory->buildMailer();
        $mailer->send($email);
    }

    public function enableDryRun(): self
    {
        if ($this->dryRun) {
            return $this;
        }

        $this->dryRun = true;
        $this->logger->info('Performing dry run...');

        return $this;
    }

    /**
     * @return string
     */
    private function getUserFilename(string $username): string
    {
        return $this->configurationService['[users_path]'] . '/' . $username . '.yaml';
    }

    public function changePasswordForAllUsers(): void
    {
        $users = $this->getUsers();

        /** @var User $user */
        foreach ($users as $user) {
            $this->changePasswordForUser($user);
        }
    }
}
