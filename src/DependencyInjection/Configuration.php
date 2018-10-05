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
                ->booleanNode('isDebug')->defaultTrue()->info('Set false for production environments')->end()
                ->scalarNode('who')->defaultValue('Abeh')->info('Set who is the greeting for')->end()
                ->scalarNode('salam_provider')->defaultNull()->info('Custom salam provider if there is')->end()
            ->end();
        return $treeBuilder;


    }

}