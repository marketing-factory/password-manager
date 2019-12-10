<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform;

use Doctrine\DBAL\Connection;

/**
 * Interface DatabaseUpdaterInterface
 * @package Mfc\PasswordManager\Platform
 * @author Christian Spoo <cs@marketing-factory.de>
 */
interface DatabaseUpdaterInterface
{
    /**
     * @param Connection $databaseConnection
     * @return mixed
     */
    public function setDatabaseConnection(Connection $databaseConnection);
}
