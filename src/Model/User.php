<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Model;

/**
 * Class User
 * @package Mfc\PasswordManager\Model
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class User
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $firstname;
    /**
     * @var string
     */
    private $lastname;
    /**
     * @var string
     */
    private $email;
    /**
     * @var array
     */
    private $hashedPasswords = [];
    /**
     * @var bool
     */
    private $active;

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname(): string
    {
        return $this->firstname;
    }

    /**
     * @return User
     */
    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname(): string
    {
        return $this->lastname;
    }

    /**
     * @return User
     */
    public function setLastname(string $lastname): User
    {
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return array
     */
    public function getHashedPasswords(): array
    {
        return $this->hashedPasswords;
    }

    /**
     * @return User
     */
    public function setHashedPasswords(array $hashedPasswords): User
    {
        $this->hashedPasswords = $hashedPasswords;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;
        return $this;
    }
}
