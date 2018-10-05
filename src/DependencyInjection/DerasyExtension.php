<?php

namespace Derasy\DerasyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;


class DerasyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        // var_dump($configs);die;
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        // Load config
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        // Put config to service
        $definition = $container->getDefinition('derasy_bundle.derasy_greeting');
        $definition->setArgument(1, $config['isDebug']);
        $definition->setArgument(2, $config['who']);

        if (null !== $config["salam_provider"]) {
            // $definition->setArgument(0, new Reference($config['salam_provider']));
            $container->setAlias('derasy_bundle.salam_provider', $config['salam_provider']);
        }
    }

}