<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Doctrine\DBAL\Connection;
use Mfc\PasswordManager\Platform\Database\AbstractTransaction;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateBeSecurePwLastChangeTransaction
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class UpdateBeSecurePwLastChangeTransaction extends AbstractTransaction
{
    /**
     * UpdateBeSecurePwLastChangeTransaction constructor.
     * @param Connection $databaseConnection
     * @param LoggerInterface $logger
     * @param string $username
     */
    public function __construct(
        Connection $databaseConnection,
        LoggerInterface $logger,
        private readonly string $username
    ) {
        parent::__construct($databaseConnection, $logger);
    }

    protected function executeQueries(): void
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->update('be_users')
            ->set('tx_besecurepw_lastpwchange', $this->databaseConnection->quote((string)time()))
            ->where($queryBuilder->expr()->eq('username', $this->databaseConnection->quote($this->username)));

        $this->logger->debug($query->getSQL());
        $query->executeStatement();
    }
}
