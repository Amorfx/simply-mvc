<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\DefaultValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestAttributeValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\RequestValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\ServiceValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\SessionValueResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver\VariadicValueResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadataFactory;
use function Symfony\Component\DependencyInjection\Loader\Configurator\abstract_arg;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

/**
 * Configuration for controller
 * @see https://github.com/symfony/framework-bundle/blob/5.3/Resources/config/web.php
 */
return static function (ContainerConfigurator $container) {
    $container->services()
        ->set('controller_resolver', ControllerResolver::class)
        ->args([
            service('service_container'),
        ])
        ->tag('monolog.logger', ['channel' => 'request'])

        ->set('argument_metadata_factory', ArgumentMetadataFactory::class)
        ->set('argument_resolver', ArgumentResolver::class)
        ->args([
            service('argument_metadata_factory'),
            abstract_arg('argument value resolvers'),
        ])

        ->set('argument_resolver.request_attribute', RequestAttributeValueResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => 100])

        ->set('argument_resolver.request', RequestValueResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => 50])

        ->set('argument_resolver.session', SessionValueResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => 50])

        ->set('argument_resolver.service', ServiceValueResolver::class)
        ->args([
            abstract_arg('service locator, set in RegisterControllerArgumentLocatorsPass'),
        ])
        ->tag('controller.argument_value_resolver', ['priority' => -50])

        ->set('argument_resolver.default', DefaultValueResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => -100])

        ->set('argument_resolver.variadic', VariadicValueResolver::class)
        ->tag('controller.argument_value_resolver', ['priority' => -150])
    ;
};
