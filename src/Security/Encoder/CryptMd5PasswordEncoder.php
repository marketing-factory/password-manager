<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class CryptMd5PasswordEncoder
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class CryptMd5PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encodePassword(string $password): string
    {
        return crypt($password, '$1$$');
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithmKey(): string
    {
        return AccountUpdaterInterface::ALGO_CRYPT_MD5;
    }
}
