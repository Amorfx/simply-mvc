<?php

namespace Simply\Mvc;

class WordPressRouteMatcher {
    private static $mappConditions = array(
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

    public function match(Route $route, $request): bool {
        $result = false;
        if (array_key_exists($route->getWordpressCondition(), self::$mappConditions)) {
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
