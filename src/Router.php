<?php

namespace Simply\Mvc;

class Router {
    /**
     * @var array<Route>
     */
    private array $routes = array();

    public function add(Route $route) {
        $this->routes[] = $route;
    }

    public function getAll(): array {
        return $this->routes;
    }
}
