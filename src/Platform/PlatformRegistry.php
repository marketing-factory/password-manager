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
    private $updaters = [];

    private const PLATFORM_MAPPINGS = [
        'typo3' => Typo3Updater::class,
        'typo3_9' => Typo3Updater::class,
        'typo3_8' => Typo3v7Updater::class,
        'typo3_7' => Typo3v7Updater::class,
        'typo3_6' => Typo3v6Updater::class,
    ];
    /**
     * @var ConfigurationService
     */
    private $configurationService;

    /**
     * PlatformRegistry constructor.
     * @param ConfigurationService $configurationService
     */
    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * @param AccountUpdaterInterface $updater
     */
    public function registerUpdater(AccountUpdaterInterface $updater): void
    {
        $supportedTypes = $updater->getSupportedPlatformTypes();

        foreach ($supportedTypes as $supportedType) {
            $this->updaters[$supportedType] = $updater;
        }
    }

    /**
     * @param Platform $platform
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
