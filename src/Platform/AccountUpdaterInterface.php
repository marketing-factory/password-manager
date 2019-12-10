<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform;

/**
 * Interface AccountUpdaterInterface
 * @package Mfc\PasswordManager\Platform
 * @author Christian Spoo <cs@marketing-factory.de>
 */
interface AccountUpdaterInterface
{
    public const ALGO_ARGON2I = 'argon2i';
    public const ALGO_BCRYPT = 'bcrypt';
    public const ALGO_CRYPT_MD5 = 'crypt_md5';
    public const ALGO_SHA1 = 'sha1';
    public const ALGO_MD5 = 'md5';

    /**
     * @return array
     */
    public function getSupportedPlatformTypes(): array;

    /**
     * @return string
     */
    public function getHashAlgorithm(): string;

    /**
     * @param string $username
     * @param string $passwordHash
     * @param bool $isAdmin
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @return bool
     */
    public function updateAccountByUsername(
        string $username,
        string $passwordHash,
        bool $isAdmin,
        string $firstname,
        string $lastname,
        string $email
    ): bool;

    /**
     * @param array|string[] $whitelistedAdmins
     * @return bool
     */
    public function demoteUnknownUsers(array $whitelistedAdmins): bool;
}
