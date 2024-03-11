<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Tests\unit\Services;

use Mfc\PasswordManager\Services\ConfigurationService;
use Mfc\PasswordManager\Tests\UnitTester;

/**
 * Class ConfigurationServiceCest
 * @package Mfc\PasswordManager\Tests\unit\Services
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class ConfigurationServiceCest
{
    public function testIfValidConfigurationGetsReadCorrectly(UnitTester $I): void
    {
        $configurationService = new ConfigurationService();
        $configurationService->loadConfiguration(__DIR__ . '/../../_data/config/config_valid.yaml');

        $I->assertEquals('db_pusher_user', $configurationService['[database][default_credentials][username]']);
        $I->assertEquals('test1234', $configurationService['[database][default_credentials][password]']);
    }
}
