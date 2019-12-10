<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class Md5PasswordEncoder
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Md5PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encodePassword(string $password): string
    {
        return md5($password);
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithmKey(): string
    {
        return AccountUpdaterInterface::ALGO_MD5;
    }
}
