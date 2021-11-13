<?php

use Simply\Mvc\BasicController;
use Simply\Mvc\DependencyInjection\FrameworkExtension;
use Simply\Mvc\Routing\Route;
use Simply\Mvc\Routing\Router;

require __DIR__ . '/vendor/autoload.php';

add_filter('simply_config_directories', function (array $configurationsPath) {
    $configurationsPath[] = __DIR__ . '/config';
    return $configurationsPath;
});

add_filter('simply_container_extensions', function(array $extensions) {
    $extensions[] = new FrameworkExtension();
    return $extensions;
});

add_action('after_setup_theme', function() {
    $router = Simply::get(Router::class);
    $basicRoute = new Route('front', array('GET'), BasicController::class, 'home');
    $router->add($basicRoute);
});

