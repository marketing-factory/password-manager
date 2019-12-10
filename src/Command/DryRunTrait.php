<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Trait DryRunTrait
 * @package Mfc\PasswordManager\Command
 * @author Christian Spoo <cs@marketing-factory.de>
 */
trait DryRunTrait
{
    /**
     * @var bool
     */
    protected $dryRun = false;

    protected function configureDryRun(): self
    {
        $this->addOption('dry-run', null, InputOption::VALUE_NONE, 'Execute the operation as a dry run.');
        return $this;
    }

    protected function checkDryRun(InputInterface $input)
    {
        if (true === $input->getOption('dry-run')) {
            $this->dryRun = true;
        }
    }
}
