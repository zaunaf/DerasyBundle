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
        $loader->load('console.xml');

        // Load config
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $projectDir = $container->getParameter('kernel.project_dir');

        // Prepare configs
        $config["project_dir"] = $projectDir;
        $propelConfig = $container->getParameter('propel.configuration');
        // $container->

        // Put config to service
        $definition = $container->getDefinition('derasy_bundle.derasy_greeting');
        $definition->setArgument(1, $config['isDebug']);
        $definition->setArgument(2, $config['who']);

        // Put config to Reverse Command Service Definition
        $reverseServiceDefinition = $container->getDefinition('derasy_bundle.reverse');
        $reverseServiceDefinition->setArgument(0, $config);
        $reverseServiceDefinition->setArgument(1, $propelConfig);
        $reverseServiceDefinition->setArgument(2, new Reference('logger'));

        $buildModelServiceDefinition = $container->getDefinition('derasy_bundle.build_model');
        $buildModelServiceDefinition->setArgument(0, $config);
        $buildModelServiceDefinition->setArgument(1, $propelConfig);
        $buildModelServiceDefinition->setArgument(2, new Reference('logger'));

        $buildOrmServiceDefinition = $container->getDefinition('derasy_bundle.build_orm');
        $buildOrmServiceDefinition->setArgument(0, $config);
        $buildOrmServiceDefinition->setArgument(1, $propelConfig);
        $buildOrmServiceDefinition->setArgument(2, new Reference('logger'));

        $buildUiAndroidServiceDefinition = $container->getDefinition('derasy_bundle.build_ui_android');
        $buildUiAndroidServiceDefinition->setArgument(0, $config);
        $buildUiAndroidServiceDefinition->setArgument(1, $propelConfig);
        $buildUiAndroidServiceDefinition->setArgument(2, new Reference('logger'));


        $buildUiExt6ServiceDefinition = $container->getDefinition('derasy_bundle.build_ui_ext6');
        $buildUiExt6ServiceDefinition->setArgument(0, $config);
        $buildUiExt6ServiceDefinition->setArgument(1, $propelConfig);
        $buildUiExt6ServiceDefinition->setArgument(2, new Reference('logger'));

        $buildUiNebularServiceDefinition = $container->getDefinition('derasy_bundle.build_ui_nebular');
        $buildUiNebularServiceDefinition->setArgument(0, $config);
        $buildUiNebularServiceDefinition->setArgument(1, $propelConfig);
        $buildUiNebularServiceDefinition->setArgument(2, new Reference('logger'));

        $buildUiFusereactServiceDefinition = $container->getDefinition('derasy_bundle.build_ui_fusereact');
        $buildUiFusereactServiceDefinition->setArgument(0, $config);
        $buildUiFusereactServiceDefinition->setArgument(1, $propelConfig);
        $buildUiFusereactServiceDefinition->setArgument(2, new Reference('logger'));

        $patchPropelServiceDefinition = $container->getDefinition('derasy_bundle.patch_propel');
        $patchPropelServiceDefinition->setArgument(0, $config);
        $patchPropelServiceDefinition->setArgument(1, $propelConfig);
        $patchPropelServiceDefinition->setArgument(2, new Reference('logger'));

        $generatePasswordServiceDefinition = $container->getDefinition('derasy_bundle.generate_password');
        $generatePasswordServiceDefinition->setArgument(0, $config);
        $generatePasswordServiceDefinition->setArgument(1, new Reference('security.password_encoder'));
        $generatePasswordServiceDefinition->setArgument(2, new Reference('logger'));

        // $reverseServiceDefinition->setArgument(2, $rootFolder);


        if (null !== $config["salam_provider"]) {
            // $definition->setArgument(0, new Reference($config['salam_provider']));
            $container->setAlias('derasy_bundle.salam_provider', $config['salam_provider']);
        }
    }

}