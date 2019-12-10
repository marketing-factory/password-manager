<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\DependencyInjection\Compiler;

use Mfc\PasswordManager\Platform\AccountUpdaterInterface;
use Mfc\PasswordManager\Platform\PlatformRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class AccountUpdaterPass
 * @package Mfc\PasswordManager\DependencyInjection\Compiler
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class AccountUpdaterPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     */
    public function process(ContainerBuilder $container)
    {
        $registryDefinition = $container->findDefinition(PlatformRegistry::class);

        foreach ($container->findTaggedServiceIds('account_updater') as $id => $tags) {
            $registryDefinition->addMethodCall(
                'registerUpdater',
                [
                    new Reference($id)
                ]
            );
        }
    }
}
