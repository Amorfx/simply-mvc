<?php

namespace Simply\Mvc\DependencyInjection;

use Simply\Mvc\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class FrameworkExtension extends Extension {
    public function load(array $configs, ContainerBuilder $container) {
        $container->registerForAutoconfiguration(AbstractController::class)
            ->addTag('controller.service_arguments')
            ->addTag('container.service_subscriber');
    }
}
