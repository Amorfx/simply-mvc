<?php

namespace Simply\Mvc\Tests\Controller;

use Simply\Mvc\Controller\Resolvers\WpQueryResolver;
use Simply\Mvc\Tests\MvcTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class WpQueryResolverTest extends MvcTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $GLOBALS['wp_query'] = new \stdClass();
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['wp_query']);
        parent::tearDown();
    }

    public function testIsASymfony7CompatibleResolver(): void
    {
        $this->assertInstanceOf(ValueResolverInterface::class, new WpQueryResolver());
    }

    public function testResolvesTheGlobalQueryByType(): void
    {
        $values = (new WpQueryResolver())->resolve(new Request(), new ArgumentMetadata('query', \WP_Query::class, false, false, null));

        $this->assertSame([$GLOBALS['wp_query']], [...$values]);
    }

    public function testResolvesTheGlobalQueryByName(): void
    {
        $values = (new WpQueryResolver())->resolve(new Request(), new ArgumentMetadata('wp_query', null, false, false, null));

        $this->assertSame([$GLOBALS['wp_query']], [...$values]);
    }

    public function testIgnoresOtherArguments(): void
    {
        $values = (new WpQueryResolver())->resolve(new Request(), new ArgumentMetadata('id', 'int', false, false, null));

        $this->assertSame([], [...$values]);
    }
}
