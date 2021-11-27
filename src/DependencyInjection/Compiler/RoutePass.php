<?php

namespace Simply\Mvc\DependencyInjection\Compiler;

use Simply\Mvc\Attribute\Route;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Read all attributes Route in Controller
 */
class RoutePass implements CompilerPassInterface {
    /**
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container) {
        $allController = $container->findTaggedServiceIds('simply.controller');
        $allRoutes = array();
        foreach ($allController as $serviceIds => $tag) {
            $definition = $container->findDefinition($serviceIds);
            $reflection = $container->getReflectionClass($definition->getClass());
            $methods = $reflection->getMethods();
            foreach ($methods as $m) {
                $attrs = $m->getAttributes(Route::class);
                foreach ($attrs as $attr) {
                    /** @var Route $route */
                    $route = $attr->newInstance();
                    $allRoutes[] = new \Simply\Mvc\Routing\Route(
                        $route->getWpCondition(),
                        $route->getMethods(),
                        $definition->getClass(),
                        $m->getName(),
                        $route->getCustomCondition()
                    );
                }
            }
        }
        $container->setParameter('simply.routes', $allRoutes);
    }
}
