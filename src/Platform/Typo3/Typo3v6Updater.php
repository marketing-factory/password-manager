<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Platform\Typo3;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;

/**
 * Class Typo3v6Updater
 * @package Mfc\PasswordManager\Platform\Typo3
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Typo3v6Updater extends Typo3Updater
{
    /**
     * @return string
     */
    public function getHashAlgorithm(): string
    {
        return AccountUpdaterInterface::ALGO_CRYPT_MD5;
    }

    /**
     * @return array
     */
    public function getSupportedPlatformTypes(): array
    {
        return [
            'typo3_6',
        ];
    }
}
