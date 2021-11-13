<?php

namespace Simply\Mvc\DependencyInjection;

use Simply\Mvc\Controller\AbstractController;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;

class FrameworkExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container) {
        $fileLocator = new FileLocator(dirname(__DIR__, 2) .'/config');
        $loader = new PhpFileLoader($container, $fileLocator);
        $loaderYaml = new YamlFileLoader($container, $fileLocator);
        $loaderYaml->load('simply-mvc.yml');
        $loader->load('controller.php');

        $container->registerForAutoconfiguration(ArgumentValueResolverInterface::class)
            ->addTag('controller.argument_value_resolver');
        $container->registerForAutoconfiguration(AbstractController::class)
            ->addTag('controller.service_arguments');
    }
}
