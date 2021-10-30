<?php

use Simply\Mvc\Route;
use Simply\Mvc\Router;
use Simply\Mvc\WordPressRouteMatcher;

require __DIR__ . '/vendor/autoload.php';

$router = new Router();
$basicRoute = new Route('front', array('GET'), \Simply\Mvc\BasicController::class, 'home');
$router->add($basicRoute);
add_filter('template_include', function (string $template) use($router) {
    $matcher = new WordPressRouteMatcher();
    foreach ($router->getAll() as $r) {
        if ($matcher->match($r, 'ok')) {
            // Todo get with container
            $controller = $r->getController();
            $controller = new $controller();
            call_user_func(array($controller, $r->getAction()));
            return false;
        }
    }
    return $template;
});
