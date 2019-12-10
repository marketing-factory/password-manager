<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Exception\RetryableException;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTransaction
 * @package Mfc\PasswordManager\Platform\Database
 * @author Christian Spoo <cs@marketing-factory.de>
 */
abstract class AbstractTransaction
{
    /**
     * @var Connection
     */
    protected $databaseConnection;
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * AbstractTransaction constructor.
     * @param Connection $databaseConnection
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $databaseConnection, LoggerInterface $logger)
    {
        $this->databaseConnection = $databaseConnection;
        $this->logger = $logger;
    }

    abstract protected function executeQueries(): void;

    public function execute()
    {
        $this->logger->info(get_class($this) . ' (Begin)');
        try {
            $this->executeQueries();

            $this->logger->info(get_class($this) . ' (Commit)');
        } catch (RetryableException $e) {
            throw $e;
        } catch (DBALException $e) {
            $this->logger->error('DBAL exception caught: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            $this->logger->error('Transaction ' . get_class($this) . ' failed', [ 'transaction' => __CLASS__ ]);

            $originalException = $e;
            do {
                $this->logger->error(strtok($e->getMessage(), PHP_EOL));
                $this->logger->debug(
                    get_class($e)
                    . ' thrown in ' . $e->getFile()
                    . ' @ ' . $e->getLine()
                    . PHP_EOL . $e->getTraceAsString()
                );

                $e = $e->getPrevious();
            } while ($e->getPrevious() instanceof \Exception);

            $this->logger->info(get_class($this) . ' (Rollback)');

            throw new TransactionFailedException('Transaction failed', 1503647119, $originalException);
        }
    }
}
