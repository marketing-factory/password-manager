<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\Platform;

/**
 * Class Platform
 * @package Mfc\PasswordManager\Platform
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Platform
{
    /**
     * Platform constructor.
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(
        private readonly string $hostname,
        private readonly string $type,
        private readonly string $database,
        private readonly bool $manageAdminUsers = false,
        private readonly ?string $username = null,
        private readonly ?string $password = null
    ) {
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * @return bool
     */
    public function getManageAdminUsers(): bool
    {
        return $this->manageAdminUsers;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
