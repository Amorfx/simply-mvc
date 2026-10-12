<?php

namespace Simply\Mvc\Controller\Resolvers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use WP_Query;

/**
 * Injects the main WordPress query in controller arguments typed WP_Query or named $wp_query
 */
final class WpQueryResolver implements ValueResolverInterface {
    /**
     * @return iterable<\WP_Query>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable {
        $supported = $argument->getType() === WP_Query::class
            || $argument->getName() === 'wp_query'
            || $argument->getName() === 'WP_Query';

        return $supported ? [$GLOBALS['wp_query']] : [];
    }
}
