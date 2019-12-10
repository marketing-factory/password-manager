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
     * @param string $username
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
     * @param string $firstname
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
     * @param string $lastname
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
     * @param string $email
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
     * @param array $hashedPasswords
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
     * @param bool $active
     * @return User
     */
    public function setActive(bool $active): User
    {
        $this->active = $active;
        return $this;
    }
}
