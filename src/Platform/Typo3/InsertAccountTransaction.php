<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Mfc\PasswordManager\Platform\Database\AbstractInsertTransaction;

/**
 * Class InsertAccountTransaction
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class InsertAccountTransaction extends AbstractInsertTransaction
{
    protected function executeQueries(): void
    {
        $queryBuilder = $this->databaseConnection->createQueryBuilder();
        $query = $queryBuilder
            ->insert('be_users')
            ->values($this->getData())
        ;

        $this->logger->debug($query->getSQL());
        $query->execute();

        $this->setLastInsertId((int)$this->databaseConnection->lastInsertId());
    }
}
