<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Doctrine\DBAL\Connection;
use Mfc\PasswordManager\Platform\Database\AbstractTransaction;
use Psr\Log\LoggerInterface;

/**
 * Class DemoteUnknownAccountsTransaction
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class DemoteUnknownAccountsTransaction extends AbstractTransaction
{
    /**
     * @var array
     */
    private $whitelistedAccounts;

    /**
     * UpdateAccountTransaction constructor.
     * @param Connection $databaseConnection
     * @param LoggerInterface $logger
     * @param array $whitelistedAccounts
     */
    public function __construct(
        Connection $databaseConnection,
        LoggerInterface $logger,
        array $whitelistedAccounts
    ) {
        parent::__construct($databaseConnection, $logger);
        $this->whitelistedAccounts = $whitelistedAccounts;
    }

    protected function executeQueries(): void
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->update('be_users')
            ->set('admin', $this->databaseConnection->quote(0))
            ->where($queryBuilder->expr()->notIn(
                'username',
                array_map([$this->databaseConnection, 'quote'], $this->whitelistedAccounts)
            ));

        $this->logger->debug($query->getSQL());
        $query->execute();
    }
}
