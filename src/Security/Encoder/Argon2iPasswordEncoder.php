<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class Argon2iPasswordEncoder
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Argon2iPasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encodePassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithmKey(): string
    {
        return AccountUpdaterInterface::ALGO_ARGON2I;
    }
}
