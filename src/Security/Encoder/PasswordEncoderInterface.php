<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Security\Encoder;

/**
 * Interface PasswordEncoderInterface
 * @package Mfc\PasswordManager\Security\Encoder
 * @author Christian Spoo <cs@marketing-factory.de>
 */
interface PasswordEncoderInterface
{
    /**
     * @param string $password
     * @return string
     */
    public function encodePassword(string $password): string;

    /**
     * @return string
     */
    public function getAlgorithmKey(): string;
}
