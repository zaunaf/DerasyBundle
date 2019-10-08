<?php

namespace Derasy\DerasyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface

{
    public function getConfigTreeBuilder()
    {
        // TODO: Implement getConfigTreeBuilder() method.
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('derasy');
        $rootNode
            ->children()
                ->arrayNode('skip_db')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('skip_schema')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('skip_table')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('skip_column')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('data_schema')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('reference_schema')
                    ->prototype('scalar')
                    ->end()
                ->end()
                ->arrayNode('report_schema')
                    ->prototype('scalar')
                    ->end()
                ->end()
            ->arrayNode('auth')
                ->children()
                    ->scalarNode('user_table')->defaultValue('app_user')->info('Set user table')->end()
                    ->scalarNode('username')->defaultValue('username')->info('Set username column')->end()
                    ->scalarNode('password')->defaultValue('password')->info('Set password column')->end()
                    ->arrayNode('roles')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                    ->scalarNode('default_role')->defaultValue('operator')->info('Set default role')->end()
            ->end()
            ->end()
            ->scalarNode('android_project_folder')->defaultValue(false)->info('Set android project folder.')->end()
            ->booleanNode('isDebug')->defaultTrue()->info('Set false for production environments')->end()
            ->scalarNode('who')->defaultValue('Abeh')->info('Set who is the greeting for')->end()
            ->scalarNode('salam_provider')->defaultNull()->info('Custom salam provider if there is')->end()
            ->end();
        return $treeBuilder;


    }

}