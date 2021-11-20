<?php

namespace Simply\Mvc\Routing;

class Router {
    /**
     * @var array<Route>
     */
    private array $routes = array();

    public function add(Route $route) {
        $this->routes[] = $route;
    }

    public function addMultiple(array $routes) {
        $this->routes = array_merge($this->routes, $routes);
    }

    public function getAll(): array {
        return $this->routes;
    }
}
