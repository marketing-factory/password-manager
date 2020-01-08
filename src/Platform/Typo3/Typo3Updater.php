<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;
use Mfc\PasswordManager\Platform\DatabaseUpdaterInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

/**
 * Class Typo3Updater
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Typo3Updater implements AccountUpdaterInterface, DatabaseUpdaterInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Typo3Updater constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $username
     * @param string $passwordHash
     * @param bool $isAdmin
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param bool $isActive
     * @return bool
     */
    public function updateAccountByUsername(
        string $username,
        string $passwordHash,
        bool $isAdmin,
        string $firstname,
        string $lastname,
        string $email,
        bool $isActive = true
    ): bool {
        try {
            $accountPresent = $this->accountIsPresent($username);
        } catch (\Exception $e) {
            $this->logger->error('Could not determine whether account is present: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);

            return false;
        }

        if ($accountPresent) {
            (new UpdateAccountTransaction(
                $this->databaseConnection,
                $this->logger,
                $username,
                [
                    'password' => $this->databaseConnection->quote($passwordHash),
                    'realName' => $this->databaseConnection->quote(
                        sprintf(
                            '%s %s',
                            $firstname,
                            $lastname
                        )
                    ),
                    'email' => $this->databaseConnection->quote($email),
                    'admin' => $this->databaseConnection->quote($isAdmin ? 1 : 0),
                    'disable' => $this->databaseConnection->quote($isActive ? 0 : 1),
                    'deleted' => $this->databaseConnection->quote(0),
                ]
            ))->execute();
        } else {
            $userId = (new InsertAccountTransaction(
                $this->databaseConnection,
                $this->logger,
                [
                    'username' => $this->databaseConnection->quote($username),
                    'password' => $this->databaseConnection->quote($passwordHash),
                    'realName' => $this->databaseConnection->quote(
                        sprintf(
                            '%s %s',
                            $firstname,
                            $lastname
                        )
                    ),
                    'email' => $this->databaseConnection->quote($email),
                    'admin' => $this->databaseConnection->quote($isAdmin ? 1 : 0),
                    'deleted' => $this->databaseConnection->quote(0),
                    'disable' => $this->databaseConnection->quote($isActive ? 0 : 1),
                    'tstamp' => $this->databaseConnection->quote(time()),
                    'crdate' => $this->databaseConnection->quote(time()),
                ]
            ))->execute();

            $this->logger->debug('New user {username} created with ID {user_id}', [
                'username' => $username,
                'user_id' => $userId
            ]);
        }

        if ($this->beSecurePwIsPresent($username)) {
            $this->logger->debug('Found be_secure_pw. Updating date of last change for {username}', [
                'username' => $username
            ]);

            (new UpdateBeSecurePwLastChangeTransaction(
                $this->databaseConnection,
                $this->logger,
                $username
            ))->execute();
        }

        return true;
    }

    /**
     * @param string $username
     * @return bool
     */
    private function beSecurePwIsPresent(string $username): bool
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->select('*')
            ->from('be_users')
            ->where($queryBuilder->expr()->eq('username', $this->databaseConnection->quote($username)))
            ->setMaxResults(1);
        $this->logger->debug($query->getSQL());

        $statement = $query->execute();
        if (!($statement->rowCount() > 0)) {
            return false;
        }

        $row = $statement->fetch();
        return isset($row['tx_besecurepw_lastpwchange']);
    }

    /**
     * @param string $username
     * @return bool
     */
    private function accountIsPresent(string $username): bool
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->select('username')
            ->from('be_users')
            ->where($queryBuilder->expr()->eq('username', $this->databaseConnection->quote($username)))
            ->setMaxResults(1);
        $this->logger->debug($query->getSQL());

        $statement = $query->execute();

        $found = ($statement->rowCount() > 0);
        return $found;
    }

    /**
     * @param array|string[] $whitelistedAdmins
     * @return bool
     */
    public function demoteUnknownUsers(array $whitelistedAdmins): bool
    {
        (new DemoteUnknownAccountsTransaction(
            $this->databaseConnection,
            $this->logger,
            $whitelistedAdmins
        ))->execute();

        return true;
    }

    /**
     * @param Connection $databaseConnection
     * @return mixed
     */
    public function setDatabaseConnection(Connection $databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * @return string
     */
    public function getHashAlgorithm(): string
    {
        return AccountUpdaterInterface::ALGO_ARGON2I;
    }

    /**
     * @return array
     */
    public function getSupportedPlatformTypes(): array
    {
        return [
            'typo3',
            'typo3_9',
        ];
    }
}
