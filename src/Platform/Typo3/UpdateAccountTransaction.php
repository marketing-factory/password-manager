<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Doctrine\DBAL\Connection;
use Mfc\PasswordManager\Platform\Database\AbstractTransaction;
use Psr\Log\LoggerInterface;

/**
 * Class UpdateAccountTransaction
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class UpdateAccountTransaction extends AbstractTransaction
{
    /**
     * UpdateAccountTransaction constructor.
     * @param Connection $databaseConnection
     * @param LoggerInterface $logger
     * @param string $username
     * @param array $data
     */
    public function __construct(
        Connection $databaseConnection,
        LoggerInterface $logger,
        private readonly string $username,
        private readonly array $data
    ) {
        parent::__construct($databaseConnection, $logger);
    }

    protected function executeQueries(): void
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->update('be_users')
            ->where($queryBuilder->expr()->eq('username', $this->databaseConnection->quote($this->username)));

        foreach ($this->data as $key => $value) {
            $query->set($key, $value);
        }

        $this->logger->debug($query->getSQL());
        $query->executeStatement();
    }
}
