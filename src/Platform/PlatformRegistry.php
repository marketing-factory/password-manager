<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Driver\PDOConnection;
use Doctrine\DBAL\DriverManager;
use Mfc\PasswordManager\Platform\Typo3\Typo3Updater;
use Mfc\PasswordManager\Platform\Typo3\Typo3v6Updater;
use Mfc\PasswordManager\Platform\Typo3\Typo3v7Updater;
use Mfc\PasswordManager\Services\ConfigurationService;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

/**
 * Class PlatformRegistry
 * @package Mfc\PasswordManager\Platform
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class PlatformRegistry
{
    /**
     * @var array
     */
    private array $updaters = [];

    public function __construct(
        private readonly ConfigurationService $configurationService,
        #[TaggedIterator('account_updater')]
        iterable $updaters,
    ) {

        /** @var AccountUpdaterInterface $updater */
        foreach ($updaters as $updater) {
            $supportedTypes = $updater->getSupportedPlatformTypes();

            foreach ($supportedTypes as $supportedType) {
                $this->updaters[$supportedType] = $updater;
            }
        }
    }

    /**
     * @return AccountUpdaterInterface
     */
    public function getUpdaterForPlatform(Platform $platform): AccountUpdaterInterface
    {
        $type = $platform->getType();
        if (!isset($this->updaters[$type])) {
            throw new \InvalidArgumentException("Unknown platform type \"{$type}\"");
        }

        $updater = $this->updaters[$type];
        if ($updater instanceof DatabaseUpdaterInterface) {
            $username = $this->configurationService['[database][default_credentials][username]'];
            $password = $this->configurationService['[database][default_credentials][password]'];

            $connectionParams = [
                'dbname' => $platform->getDatabase(),
                'username' => $username,
                'password' => $password,
                'host' => $platform->getHostname(),
                'driver' => 'pdo_mysql'
            ];

            $connection = DriverManager::getConnection($connectionParams, new Configuration());
            $updater->setDatabaseConnection($connection);
        }

        return $updater;
    }
}
