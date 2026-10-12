<?php

namespace Simply\Mvc\Routing;

use Simply\Mvc\Request;

class Router {
    /**
     * @var array<Route>
     */
    private array $routes = array();

    public function __construct(private WordPressRouteMatcher $matcher)
    {
    }

    public function add(Route $route): void {
        $this->routes[] = $route;
    }

    /**
     * @param Route[] $routes
     */
    public function addMultiple(array $routes): void {
        $this->routes = array_merge($this->routes, $routes);
    }

    /**
     * @return Route[]
     */
    public function getAll(): array {
        return $this->routes;
    }

    /**
     * Find the route to call for the request:
     * - a matching route with a custom condition wins,
     * - otherwise the matching route for the most specific template of the hierarchy,
     * - otherwise the first matching route (conditions like "front" or "tax" have no template of that name).
     *
     * @param string[] $templates WordPress template hierarchy without ".php", most specific first
     */
    public function findRoute(Request $request, array $templates): ?Route
    {
        $matchedRoutes = [];
        foreach ($this->routes as $route) {
            if (!$this->matcher->match($route, $request, $templates)) {
                continue;
            }

            if ($route->getCustomCondition() !== false) {
                return $route;
            }

            $matchedRoutes[] = $route;
        }

        foreach ($templates as $template) {
            foreach ($matchedRoutes as $route) {
                if ($route->getWordpressCondition() === $template) {
                    return $route;
                }
            }
        }

        return $matchedRoutes[0] ?? null;
    }
}
