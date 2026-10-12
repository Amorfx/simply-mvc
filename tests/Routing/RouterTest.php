<?php

namespace Simply\Mvc\Tests\Routing;

use Brain\Monkey;
use Simply\Mvc\Request;
use Simply\Mvc\Routing\Route;
use Simply\Mvc\Routing\Router;
use Simply\Mvc\Routing\WordPressRouteMatcher;
use Simply\Mvc\Tests\MvcTestCase;

class RouterTest extends MvcTestCase
{
    private const TEMPLATES = ['single-post', 'single', 'singular', 'index'];

    private function route(string $condition, string $action, string|bool $customCondition = false): Route
    {
        return new Route($condition, ['GET', 'POST'], 'App\\Controller', $action, $customCondition);
    }

    private function router(Route ...$routes): Router
    {
        $router = new Router(new WordPressRouteMatcher());
        $router->addMultiple($routes);
        return $router;
    }

    public function testNoRouteMatches(): void
    {
        Monkey\Functions\when('is_page')->justReturn(false);

        $this->assertNull($this->router($this->route('page', 'page'))->findRoute(Request::create('/'), self::TEMPLATES));
    }

    public function testMostSpecificTemplateWins(): void
    {
        $singular = $this->route('singular', 'singular');
        $single = $this->route('single', 'single');

        $this->assertSame($single, $this->router($singular, $single)->findRoute(Request::create('/'), self::TEMPLATES));
    }

    public function testRouteWithCustomConditionWins(): void
    {
        Monkey\Functions\when('my_condition')->justReturn(true);
        $single = $this->route('single', 'single');
        $custom = $this->route('singular', 'custom', 'my_condition');

        $this->assertSame($custom, $this->router($single, $custom)->findRoute(Request::create('/'), self::TEMPLATES));
    }

    public function testFrontRouteIsSelectedOnTheFrontPage(): void
    {
        Monkey\Functions\when('is_front_page')->justReturn(true);
        $front = $this->route('front', 'front');

        $this->assertSame($front, $this->router($front)->findRoute(Request::create('/'), ['front-page', 'home', 'index']));
    }

    public function testTaxRouteIsSelectedOnATaxonomyArchive(): void
    {
        Monkey\Functions\when('is_tax')->justReturn(true);
        $tax = $this->route('tax', 'tax');

        $this->assertSame($tax, $this->router($tax)->findRoute(Request::create('/genre/rock/'), ['taxonomy-genre-rock', 'taxonomy-genre', 'taxonomy', 'archive', 'index']));
    }

    /**
     * WordPress names these templates "front-page" and "taxonomy": the routes must keep that precedence
     * whatever the declaration order
     *
     * @param string[] $templates
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('competingRoutes')]
    public function testSpecificConditionWinsOverBroaderOne(string $specific, string $broader, string $conditionFunction, array $templates): void
    {
        Monkey\Functions\when($conditionFunction)->justReturn(true);
        $specificRoute = $this->route($specific, $specific);
        $broaderRoute = $this->route($broader, $broader);

        $this->assertSame($specificRoute, $this->router($specificRoute, $broaderRoute)->findRoute(Request::create('/'), $templates));
        $this->assertSame($specificRoute, $this->router($broaderRoute, $specificRoute)->findRoute(Request::create('/'), $templates));
    }

    /**
     * @return array<string, array{string, string, string, string[]}>
     */
    public static function competingRoutes(): array
    {
        return [
            'front over home' => ['front', 'home', 'is_front_page', ['front-page', 'home', 'index']],
            'front over page' => ['front', 'page', 'is_front_page', ['front-page', 'page-sample-page', 'page-2', 'page', 'singular', 'index']],
            'tax over archive' => ['tax', 'archive', 'is_tax', ['taxonomy-genre-rock', 'taxonomy-genre', 'taxonomy', 'archive', 'index']],
        ];
    }
}
