<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Services;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Mfc\PasswordManager\Model\System;
use Mfc\PasswordManager\Model\User;
use Mfc\PasswordManager\Platform\DatabaseUpdaterInterface;
use Mfc\PasswordManager\Platform\Platform;
use Mfc\PasswordManager\Platform\PlatformRegistry;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Class PasswordRolloutService
 * @package Mfc\PasswordManager\Services
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class PasswordRolloutService
{
    /**
     * @var bool
     */
    private $dryRun;

    /**
     * PasswordRolloutService constructor.
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private ConfigurationService $configurationService,
        private readonly UserStorage $userStorage,
        private readonly PlatformRegistry $platformRegistry
    ) {
    }

    public function enableDryRun(): self
    {
        $this->dryRun = true;
        $this->userStorage->enableDryRun();

        return $this;
    }

    public function rolloutUser($user): self
    {
        if (!$user instanceof User) {
            $user = $this->userStorage->loadUser($user);
        }

        $this->doRollout([$user]);

        return $this;
    }

    public function rolloutUsers(): self
    {
        $users = $this->userStorage->getUsers();
        $users = array_filter($users, fn(User $user) => $user->isActive());

        $this->doRollout($users, true);

        return $this;
    }

    /**
     * @throws Throwable
     */
    private function doRollout(array $users, bool $demoteUnknownUsers = false): void
    {
        $platforms = $this->configurationService['[platforms]'];

        $defaultUsername = $this->configurationService['[database][default_credentials][username]'];
        $defaultPassword = $this->configurationService['[database][default_credentials][password]'];

        /** @var Platform $platform */
        foreach ($platforms as $platformName => $platform) {
            try {
                $this->logger->info(
                    'Working on platform {platform} with type {type} on {hostname}',
                    [
                        'platform' => $platformName,
                        'type' => $platform->getType(),
                        'hostname' => $platform->getHostname()
                    ]
                );

                $databaseConnection = DriverManager::getConnection(
                    [
                        'dbname' => $platform->getDatabase(),
                        'user' => $platform->getUsername() ?? $defaultUsername,
                        'password' => $platform->getPassword() ?? $defaultPassword,
                        'host' => $platform->getHostname(),
                        'driver' => 'pdo_mysql'
                    ]
                );

                $updater = $this->platformRegistry->getUpdaterForPlatform($platform);
                if ($updater instanceof DatabaseUpdaterInterface) {
                    $updater->setDatabaseConnection($databaseConnection);
                }

                $activeAdmins = [];

                /** @var User $user */
                foreach ($users as $user) {
                    $this->logger->info(
                        'Rolling out user {username} to {hostname}',
                        [
                            'username' => $user->getUsername(),
                            'hostname' => $platform->getHostname()
                        ]
                    );

                    $hashAlgorithm = $updater->getHashAlgorithm();
                    $hashedPasswords = $user->getHashedPasswords();
                    if (!isset($hashedPasswords[$hashAlgorithm])) {
                        $this->logger->error(
                            'User {username} does not have a password hash for the {algorithm} algorithm. Skipping...',
                            [
                                'username' => $user->getUsername(),
                                'algorithm' => $hashAlgorithm
                            ]
                        );
                        continue;
                    }

                    if (!$this->dryRun) {
                        $updater->updateAccountByUsername(
                            $user->getUsername(),
                            $hashedPasswords[$hashAlgorithm],
                            true,
                            $user->getFirstname(),
                            $user->getLastname(),
                            $user->getEmail(),
                            $user->isActive()
                        );
                    }

                    $activeAdmins[] = $user->getUsername();
                }

                if ($demoteUnknownUsers && $platform->getManageAdminUsers()) {
                    $this->logger->info(
                        'Demoting all other admin users on {hostname}',
                        [
                            'hostname' => $platform->getHostname()
                        ]
                    );

                    if (!$this->dryRun) {
                        $updater->demoteUnknownUsers($activeAdmins);
                    }
                }
            } catch (\Exception $ex) {
                $this->logger->alert('Exception while rolling out users to {hostname}: {message}', [
                    'hostname' => $platform->getHostname(),
                    'message' => $ex->getMessage()
                ]);
            }
        }
    }
}
