<?php

namespace Simply\Mvc\Hook;

use Psr\Container\ContainerInterface;
use Simply;
use Simply\Core\Attributes\Filter;
use Simply\Core\Query\SimplyQuery;
use Simply\Mvc\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Simply\Mvc\Routing\Router;

class TemplateInclude implements ServiceSubscriberInterface {
    private ContainerInterface $container;

    protected $templates = array();

    #[Required]
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    public static function getSubscribedServices(): array {
        return array(
            Router::class,
            ArgumentResolver::class => 'argument_resolver'
        );
    }

    #[Filter('template_include')]
    public function sendResponse($template) {
        $request = Request::createFromGlobals();
        $request->setSimplyQuery(SimplyQuery::getCurrentQuery());
        $router = $this->getRouter();
        $router->addMultiple(Simply::getContainer()->getParameter('simply.routes'));

        // Remove .php for templates
        $templates = array_map(static fn(string $t): string => str_replace('.php', '', $t), $this->templates);
        $route = $router->findRoute($request, $templates);
        if (null === $route) {
            return $template;
        }

        /** @var \Simply\Mvc\Controller\ControllerResolver $controllerResolver */
        $controllerResolver = Simply::get('controller_resolver');
        $request->attributes->set('_controller', array($route->getController(), $route->getAction()));
        $controller = $controllerResolver->getController($request);
        $arguments = $this->getArgumentResolver()->getArguments($request, $controller);
        $response = call_user_func_array($controller, $arguments);
        $response->send();
        return false;
    }

    #[Filter('404_template_hierarchy')]
    #[Filter('archive_template_hierarchy')]
    #[Filter('attachment_template_hierarchy')]
    #[Filter('author_template_hierarchy')]
    #[Filter('category_template_hierarchy')]
    #[Filter('date_template_hierarchy')]
    #[Filter('embed_template_hierarchy')]
    #[Filter('frontpage_template_hierarchy')]
    #[Filter('home_template_hierarchy')]
    #[Filter('index_template_hierarchy')]
    #[Filter('page_template_hierarchy')]
    #[Filter('paged_template_hierarchy')]
    #[Filter('privacypolicy_template_hierarchy')]
    #[Filter('search_template_hierarchy')]
    #[Filter('single_template_hierarchy')]
    #[Filter('singular_template_hierarchy')]
    #[Filter('tag_template_hierarchy')]
    #[Filter('taxonomy_template_hierarchy')]
    public function addTemplates($templates) {
        $this->templates = array_unique(array_merge($this->templates, $templates));
        return $templates;
    }

    public function getRouter(): Router {
        return $this->container->get(Router::class);
    }

    public function getArgumentResolver() {
        return $this->container->get(ArgumentResolver::class);
    }
}
