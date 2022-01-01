<?php

namespace Simply\Mvc\Hook;

use Simply;
use Simply\Core\Attributes\Filter;
use Simply\Core\Query\SimplyQuery;
use Simply\Mvc\Request;
use Simply\Mvc\Routing\WordPressRouteMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Simply\Mvc\Routing\Router;

class TemplateInclude implements ServiceSubscriberInterface {
    use ServiceSubscriberTrait;

    protected $templates = array();

    public static function getSubscribedServices(): array {
        return array(
            Router::class,
            WordPressRouteMatcher::class,
            ArgumentResolver::class => 'argument_resolver'
        );
    }

    #[Filter('template_include')]
    public function sendResponse($template) {
        $request = Request::createFromGlobals();
        $request->setSimplyQuery(SimplyQuery::getCurrentQuery());
        $router = $this->getRouter();
        $routes = Simply::getContainer()->getParameter('simply.routes');
        $router->addMultiple($routes);
        $routesMatched = array();
        $priorityRoute = false;

        // Remove .php for templates
        foreach ($this->templates as $key => $t) {
            $this->templates[$key] = str_replace('.php', '', $t);
        }

        foreach ($router->getAll() as $r) {
            if ($this->getMatcher()->match($r, $request, $this->templates)) {
                if ($r->getCustomCondition() !== false) {
                    $priorityRoute = $r;
                    break;
                } else {
                    $routesMatched[] = $r;
                }
            }
        }

        /**
         * Get the priority route :
         * - Has custom condition => prior
         * - Get the priority with template hierarchy
         */
        if (!$priorityRoute) {
            foreach ($this->templates as $conditionToMatch) {
                foreach ($routesMatched as $r) {
                    if ($r->getWordpressCondition() === $conditionToMatch) {
                        $priorityRoute = $r;
                        break 2;
                    }
                }
            }
        }

        if ($priorityRoute) {
            $controller = $priorityRoute->getController();
            $controller = Simply::get($controller);
            $request->attributes->set('_controller', array($controller::class, $r->getAction()));
            $arguments = $this->getArgumentResolver()->getArguments($request, array($controller, $priorityRoute->getAction()));
            $response = call_user_func_array(array($controller, $priorityRoute->getAction()), $arguments);
            $response->send();
            return false;
        }

        return $template;
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

    public function getMatcher(): WordPressRouteMatcher {
        return $this->container->get(WordPressRouteMatcher::class);
    }

    public function getArgumentResolver() {
        return $this->container->get(ArgumentResolver::class);
    }
}
