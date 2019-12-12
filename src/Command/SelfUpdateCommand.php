<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SelfUpdateCommand
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class SelfUpdateCommand extends Command
{
    public function __construct()
    {
        parent::__construct('self-update');
    }

    public function configure()
    {
        $this->setDescription('Updates MFC password manager to the latest version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater(null, false, Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('marketing-factory/password-manager');
        $updater->getStrategy()->setPharName('pwmgr.phar');
        $updater->getStrategy()->setCurrentLocalVersion('@package_version@');

        try {
            $result = $updater->update();
            echo $result ? "Updated!\n" : "No update needed!\n";
        } catch (\Exception $e) {
            throw $e;
            exit(1);
        }
    }
}
