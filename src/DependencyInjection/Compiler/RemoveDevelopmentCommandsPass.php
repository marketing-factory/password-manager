<?php

declare(strict_types=1);

namespace Mfc\PasswordManager\DependencyInjection\Compiler;

use Symfony\Bundle\FrameworkBundle\Command\CacheClearCommand;
use Symfony\Bundle\FrameworkBundle\Command\CacheWarmupCommand;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class RemoveDevelopmentCommandsPass
 * @package Mfc\PasswordManager\DependencyInjection\Compiler
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class RemoveDevelopmentCommandsPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $commandDefinitions = $container->findTaggedServiceIds('console.command');

        foreach ($commandDefinitions as $id => $tags) {
            $commandDefinition = $container->findDefinition($id);
            $commandClass = $commandDefinition->getClass();

            if (in_array($commandClass, [CacheClearCommand::class, CacheWarmupCommand::class])) {
                $commandDefinition->addMethodCall('setHidden', [true]);
                continue;
            }

            if (str_starts_with((string)$commandClass, 'Symfony\\Bundle\\') || str_starts_with((string)$commandClass,
                    'Symfony\\Bridge\\')) {
                $container->removeDefinition($id);
            }
        }
    }
}
