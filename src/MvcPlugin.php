<?php

namespace Simply\Mvc;

use Simply\Core\Contract\PluginInterface;
use Simply\Mvc\DependencyInjection\FrameworkExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\ControllerArgumentValueResolverPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterLocaleAwareServicesPass;
use Symfony\Component\HttpKernel\DependencyInjection\RemoveEmptyControllerArgumentLocatorsPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Simply\Mvc\DependencyInjection\Compiler\RoutePass;

class MvcPlugin implements PluginInterface {
    public function build(ContainerBuilder $container): void {
        $extension = new FrameworkExtension();
        $container->registerExtension($extension);
        $container->loadFromExtension($extension->getAlias());

        $container->addCompilerPass(new RegisterControllerArgumentLocatorsPass());
        $container->addCompilerPass(new RemoveEmptyControllerArgumentLocatorsPass(), PassConfig::TYPE_BEFORE_REMOVING);
        // Add routing resolver pass simply not symfony
        $container->addCompilerPass(new ControllerArgumentValueResolverPass());
        $container->addCompilerPass(new RegisterLocaleAwareServicesPass());
        $container->addCompilerPass(new RoutePass());
    }
}
