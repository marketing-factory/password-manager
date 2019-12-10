<?php
declare(strict_types=1);

namespace Mfc\PasswordManager\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Mfc\PasswordManager\Configuration
 * @author Christian Spoo <cs@marketing-factory.de>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mfc_password_manager');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('platform_file')
                    ->defaultValue('platforms.yaml')
                ->end()
                ->arrayNode('mail')
                    ->children()
                        ->scalarNode('dsn')
                            ->defaultValue('sendmail://')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('database')
                    ->children()
                        ->arrayNode('default_credentials')
                            ->children()
                                ->scalarNode('username')
                                    ->isRequired()
                                ->end()
                                ->scalarNode('password')
                                    ->isRequired()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
