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
     * @var string
     */
    private $hostname;
    /**
     * @var string
     */
    private $database;
    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $manageAdminUsers = false;
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var string|null
     */
    private $password;

    /**
     * Platform constructor.
     * @param string $hostname
     * @param string $type
     * @param string $database
     * @param bool $manageAdminUsers
     * @param string|null $username
     * @param string|null $password
     */
    public function __construct(
        string $hostname,
        string $type,
        string $database,
        bool $manageAdminUsers = false,
        ?string $username = null,
        ?string $password = null
    ) {
        $this->hostname = $hostname;
        $this->type = $type;
        $this->database = $database;
        $this->manageAdminUsers = $manageAdminUsers;
        $this->username = $username;
        $this->password = $password;
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
