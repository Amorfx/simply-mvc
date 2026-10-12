<?php

namespace Simply\Mvc\Hook;

use Psr\Container\ContainerInterface;
use Simply;
use Simply\Core\Attributes\Filter;
use Simply\Core\Query\SimplyQuery;
use Simply\Mvc\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Simply\Mvc\Routing\Router;

class TemplateInclude implements ServiceSubscriberInterface {
    private ContainerInterface $container;

    /**
     * @var string[]
     */
    protected array $templates = [];

    private bool $routesLoaded = false;

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

    /**
     * @param mixed $template template path chosen by WordPress (filter value)
     * @return mixed the template to include, or false when a controller sent the response
     */
    #[Filter('template_include')]
    public function sendResponse(mixed $template): mixed {
        $request = $this->createRequest();

        // Remove .php for templates
        $templates = array_map(static fn(string $t): string => str_replace('.php', '', $t), $this->templates);
        $route = $this->getLoadedRouter()->findRoute($request, $templates);
        if (null === $route) {
            return $template;
        }

        /** @var \Simply\Mvc\Controller\ControllerResolver $controllerResolver */
        $controllerResolver = Simply::get('controller_resolver');
        $request->attributes->set('_controller', array($route->getController(), $route->getAction()));
        $controller = $controllerResolver->getController($request);
        $arguments = $this->getArgumentResolver()->getArguments($request, $controller);
        $response = call_user_func_array($controller, $arguments);
        if (!$response instanceof Response) {
            throw new \LogicException(sprintf(
                'The controller "%s::%s()" must return a "%s" object but it returned %s.',
                $route->getController(),
                $route->getAction(),
                Response::class,
                get_debug_type($response)
            ));
        }
        // prepare() removes the body of HEAD responses and normalises the headers
        $response->prepare($request)->send();
        return false;
    }

    /**
     * WordPress stops HEAD requests before choosing a template: let them through when a route answers,
     * so HEAD gets the same status and headers as GET.
     * The template hierarchy is not known yet, so routes matching only a template name (e.g. "single-post") are not detected.
     *
     * @param mixed $exit whether WordPress exits without generating content (filter value)
     * @return mixed
     */
    #[Filter('exit_on_http_head')]
    public function letHeadReachControllers(mixed $exit): mixed {
        if (!$exit) {
            return $exit;
        }

        return null === $this->getLoadedRouter()->findRoute($this->createRequest(), []) ? $exit : false;
    }

    private function createRequest(): Request {
        $request = Request::createFromGlobals();
        $request->setSimplyQuery(SimplyQuery::getCurrentQuery());
        return $request;
    }

    private function getLoadedRouter(): Router {
        $router = $this->getRouter();
        if (!$this->routesLoaded) {
            $router->addMultiple(Simply::getContainer()->getParameter('simply.routes'));
            $this->routesLoaded = true;
        }
        return $router;
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
    /**
     * @param mixed $templates template hierarchy (filter value)
     * @return mixed the hierarchy, unchanged
     */
    public function addTemplates(mixed $templates): mixed {
        $this->templates = array_unique(array_merge($this->templates, $templates));
        return $templates;
    }

    public function getRouter(): Router {
        return $this->container->get(Router::class);
    }

    public function getArgumentResolver(): ArgumentResolver {
        return $this->container->get(ArgumentResolver::class);
    }
}
