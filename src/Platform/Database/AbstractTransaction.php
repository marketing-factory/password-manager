<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
     * AbstractTransaction constructor.
     */
    public function __construct(protected Connection $databaseConnection, protected LoggerInterface $logger)
    {
    }

    abstract protected function executeQueries(): void;

    public function execute(): mixed
    {
        $this->logger->info(static::class . ' (Begin)');
        try {
            $this->executeQueries();
            $this->logger->info(static::class . ' (Commit)');

            return null;
        } catch (RetryableException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->logger->error('DBAL exception caught: {message}', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);
            $this->logger->error('Transaction ' . static::class . ' failed', ['transaction' => self::class]);

            $originalException = $e;
            do {
                $this->logger->error(strtok($e->getMessage(), PHP_EOL));
                $this->logger->debug(
                    $e::class
                    . ' thrown in ' . $e->getFile()
                    . ' @ ' . $e->getLine()
                    . PHP_EOL . $e->getTraceAsString()
                );

                $e = $e->getPrevious();
            } while ($e->getPrevious() instanceof \Exception);

            $this->logger->info(static::class . ' (Rollback)');

            throw new TransactionFailedException('Transaction failed', 1503647119, $originalException);
        }
    }
}
