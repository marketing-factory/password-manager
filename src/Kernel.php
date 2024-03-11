<?php

namespace Mfc\PasswordManager;

use Mfc\PasswordManager\DependencyInjection\Compiler\AccountUpdaterPass;
use Mfc\PasswordManager\DependencyInjection\Compiler\RemoveDevelopmentCommandsPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getProjectDir(): string
    {
        return \dirname(__DIR__);
    }

    protected function build(ContainerBuilder $container): void
    {
        if (!$this->debug) {
            $container->addCompilerPass(new RemoveDevelopmentCommandsPass());
        }
    }
}
