<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\RetryableException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractInsertTransaction
 * @package Mfc\PasswordManager\Platform\Database
 * @author Christian Spoo <cs@marketing-factory.de>
 */
abstract class AbstractInsertTransaction extends AbstractTransaction
{
    /**
     * @var int
     */
    private $lastInsertId;

    /**
     * @param Connection $databaseConnection
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Connection $databaseConnection,
        LoggerInterface $logger,
        private readonly array $data
    ) {
        parent::__construct($databaseConnection, $logger);
    }

    /**
     * @return int
     * @throws UndefinedLastInsertIdException
     * @throws TransactionFailedException
     */
    public function execute(): int
    {
        parent::execute();

        if ($this->lastInsertId === null) {
            throw new UndefinedLastInsertIdException('Last insert id is undefined', 1503648543);
        }

        return $this->lastInsertId;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int $lastInsertId
     */
    protected function setLastInsertId($lastInsertId)
    {
        $this->lastInsertId = (int)$lastInsertId;
    }
}
