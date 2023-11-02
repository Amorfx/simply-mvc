<?php

namespace Simply\Mvc\Controller\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class WpQueryResolver implements ValueResolverInterface {
    public function supports(Request $request, ArgumentMetadata $argumentMetadata): bool {
        return $argumentMetadata->getName() === 'WP_Query';
    }

    public function resolve(Request $request, ArgumentMetadata $argumentMetadata): \Generator {
        yield $GLOBALS['wp_query'];
    }
}
