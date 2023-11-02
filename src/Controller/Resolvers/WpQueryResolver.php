<?php

namespace Simply\Mvc\Controller\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use WP_Query;

final class WpQueryResolver implements ArgumentValueResolverInterface {
    public function supports(Request $request, ArgumentMetadata $argumentMetadata): bool {
        return $argumentMetadata->getName() === 'WP_Query'
            || $argumentMetadata->getType() === WP_Query::class
            || $argumentMetadata->getName() === 'wp_query';
    }

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): \Generator {
        yield $GLOBALS['wp_query'];
    }
}
