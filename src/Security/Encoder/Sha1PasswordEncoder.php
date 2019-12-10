<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class Sha1PasswordEncoder
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Sha1PasswordEncoder implements PasswordEncoderInterface
{
    /**
     * @inheritDoc
     */
    public function encodePassword(string $password): string
    {
        return sha1($password);
    }

    /**
     * @inheritDoc
     */
    public function getAlgorithmKey(): string
    {
        return AccountUpdaterInterface::ALGO_SHA1;
    }
}
