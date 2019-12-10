<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class BCryptPasswordEncoder
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class BCryptPasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encodePassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithmKey(): string
    {
        return AccountUpdaterInterface::ALGO_BCRYPT;
    }
}
