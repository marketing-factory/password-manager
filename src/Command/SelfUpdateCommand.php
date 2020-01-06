<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Composer\Semver\VersionParser;
use Humbug\SelfUpdate\Strategy\GithubStrategy;
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
        $currentVersion = '@package_version@';

        $updater = new Updater(null, false, Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('marketing-factory/password-manager');
        $updater->getStrategy()->setPharName('pwmgr.phar');
        $updater->getStrategy()->setCurrentLocalVersion($currentVersion);

        /**
         *
         */
        if ($currentVersion === '@package_version' . '@'
            || VersionParser::parseStability($currentVersion) !== 'stable') {
            $updater->getStrategy()->setStability(GithubStrategy::ANY);
        }

        try {
            $result = $updater->update();
            $output->writeln($result ? "Updated!" : "No update needed!");
            exit(0);
        } catch (\Exception $e) {
            throw $e;
            exit(1);
        }
    }
}
