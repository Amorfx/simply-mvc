<?php

namespace Simply\Mvc\Routing;

use Simply\Mvc\Request;

class WordPressRouteMatcher {
    /**
     * @var array<string, callable-string>
     */
    private static array $mappConditions = array(
        '404' => 'is_404',
        'archive' => 'is_archive',
        'attachment' => 'is_attachment',
        'author' => 'is_author',
        'category' => 'is_category',
        'front' => 'is_front_page',
        'home'  => 'is_home',
        'page' => 'is_page',
        'search' => 'is_search',
        'single' => 'is_single',
        'singular' => 'is_singular',
        'tag' => 'is_tag',
        'tax' => 'is_tax',
    );

    /**
     * @param string[] $templates
     */
    public function match(Route $route, Request $request, array $templates): bool {
        $result = false;

        // Verify request methods, HEAD is also answered by GET routes
        $method = $request->getMethod();
        $methods = $route->getMethods();
        if (!in_array($method, $methods, true) && !($method === 'HEAD' && in_array('GET', $methods, true))) {
            return false;
        }

        // Verify templates
        if (in_array($route->getTemplateName(), $templates, true)) {
            $result = true;
        } elseif (array_key_exists($route->getWordpressCondition(), self::$mappConditions)) {
            $result = call_user_func(self::$mappConditions[$route->getWordpressCondition()]);
            if ($result === false) {
                return false;
            }
        }
        // Verify if there is custom condition function and call it
        if ($route->getCustomCondition()) {
            if (is_callable($route->getCustomCondition())) {
                $result = call_user_func_array($route->getCustomCondition(), array($request));
            }
        }

        return $result;
    }
}
