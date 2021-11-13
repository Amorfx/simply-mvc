<?php

use Simply\Mvc\DependencyInjection\FrameworkExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;

require __DIR__ . '/vendor/autoload.php';

add_filter('simply_container_extensions', function(array $extensions) {
    $extensions[] = new FrameworkExtension();
    return $extensions;
});

add_action('simply/core/build', function(ContainerBuilder $container) {
    $container->addCompilerPass(new RegisterControllerArgumentLocatorsPass());
    $container->addCompilerPass(new RemoveEmptyControllerArgumentLocatorsPass(), PassConfig::TYPE_BEFORE_REMOVING);
    // Add routing resolver pass simply not symfony
    $container->addCompilerPass(new ControllerArgumentValueResolverPass());
    $container->addCompilerPass(new RegisterLocaleAwareServicesPass());
});

