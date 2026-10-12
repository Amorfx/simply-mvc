<?php

namespace Simply\Mvc\Tests\Hook;

use Simply\Mvc\Hook\TemplateInclude;
use Simply\Mvc\Routing\Router;
use Simply\Mvc\Tests\MvcTestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;

class TemplateIncludeTest extends MvcTestCase
{
    public function testServicesComeFromTheInjectedLocatorWithoutDeprecation(): void
    {
        $router = \Mockery::mock(Router::class);
        $hook = new TemplateInclude();

        $hook->setContainer(new ServiceLocator([Router::class => static fn() => $router]));

        $this->assertSame($router, $hook->getRouter());
    }

    public function testAddTemplatesCollectsTheHierarchyAndReturnsItUnchanged(): void
    {
        $hook = new TemplateInclude();

        $this->assertSame(['single-post.php', 'single.php'], $hook->addTemplates(['single-post.php', 'single.php']));
        $this->assertSame(['single.php', 'singular.php'], $hook->addTemplates(['single.php', 'singular.php']));
    }
}
