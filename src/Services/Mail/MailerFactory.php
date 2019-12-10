<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Services\Mail;

use Mfc\PasswordManager\Services\ConfigurationService;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class MailerFactory
 * @package Mfc\PasswordManager\Services\Mail
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class MailerFactory
{
    /**
     * @var ConfigurationService
     */
    private $configurationService;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * MailerFactory constructor.
     * @param ConfigurationService $configurationService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ConfigurationService $configurationService, EventDispatcherInterface $eventDispatcher)
    {
        $this->configurationService = $configurationService;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function buildMailer(): MailerInterface
    {
        $mailDsn = $this->configurationService['[mail][dsn]'];
        $transport = Transport::fromDsn($mailDsn);

        return new Mailer($transport, null, $this->eventDispatcher);
    }
}
