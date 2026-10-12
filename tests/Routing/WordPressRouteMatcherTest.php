<?php

namespace Simply\Mvc\Tests\Routing;

use Brain\Monkey;
use Simply\Mvc\Request;
use Simply\Mvc\Routing\Route;
use Simply\Mvc\Routing\WordPressRouteMatcher;
use Simply\Mvc\Tests\MvcTestCase;

class WordPressRouteMatcherTest extends MvcTestCase
{
    public function testHeadMatchesAGetRoute(): void
    {
        $route = new Route('single', ['GET'], 'App\\Controller', 'show');

        $this->assertTrue((new WordPressRouteMatcher())->match($route, Request::create('/', 'HEAD'), ['single']));
    }

    public function testHeadDoesNotMatchAPostOnlyRoute(): void
    {
        $route = new Route('single', ['POST'], 'App\\Controller', 'store');

        $this->assertFalse((new WordPressRouteMatcher())->match($route, Request::create('/', 'HEAD'), ['single']));
    }

    public function testMethodNotAllowed(): void
    {
        $route = new Route('single', ['GET'], 'App\\Controller', 'show');

        $this->assertFalse((new WordPressRouteMatcher())->match($route, Request::create('/', 'POST'), ['single']));
    }

    public function testConditionFunctionIsUsedWhenNotInTheTemplates(): void
    {
        Monkey\Functions\when('is_front_page')->justReturn(false);
        $route = new Route('front', ['GET'], 'App\\Controller', 'home');

        $this->assertFalse((new WordPressRouteMatcher())->match($route, Request::create('/'), ['home', 'index']));
    }
}
