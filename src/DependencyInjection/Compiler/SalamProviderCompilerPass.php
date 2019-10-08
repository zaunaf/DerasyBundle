<?php
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Created by PhpStorm.
 * User: Abah
 * Date: 08/10/2018
 * Time: 13.04
 */

class SalamProviderCompilerPass implements CompilerPassInterface {

    public function process(ContainerBuilder $container)
    {
        // TODO: Implement process() method.
        foreach ($container->findTaggedServiceIds('derasy_salam_provider') as $id => $tags) {
            var_dump($id);
        }
        die;
    }

}