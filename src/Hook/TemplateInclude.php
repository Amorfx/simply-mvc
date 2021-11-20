<?php

namespace Simply\Mvc\Hook;

use Simply;
use Simply\Core\Attributes\Filter;
use Simply\Core\Query\SimplyQuery;
use Simply\Mvc\Application;
use Simply\Mvc\Request;
use Simply\Mvc\Routing\WordPressRouteMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Simply\Mvc\Routing\Router;

class TemplateInclude implements ServiceSubscriberInterface {
    use ServiceSubscriberTrait;

    protected $templates = array();

    public static function getSubscribedServices() {
        return array(
            Router::class,
            WordPressRouteMatcher::class,
            ArgumentResolver::class => 'argument_resolver'
        );
    }

    #[Filter('template_include')]
    public function sendResponse($template) {
        var_dump($this->templates); die;
        $request = Request::createFromGlobals();
        $request->setSimplyQuery(SimplyQuery::getCurrentQuery());
        $router = $this->getRouter();
        $routes = Simply::getContainer()->getParameter('simply.routes');
        $router->addMultiple($routes);
        foreach ($router->getAll() as $r) {
            if ($this->getMatcher()->match($r, $request, $template)) {
                $controller = $r->getController();
                $controller = Simply::get($controller);
                $request->attributes->set('_controller', array($controller::class, $r->getAction()));
                $arguments = $this->getArgumentResolver()->getArguments($request, array($controller, $r->getAction()));
                $response = call_user_func_array(array($controller, $r->getAction()), $arguments);
                $response->send();
                return false;
            }
        }
        return $template;
    }

    #[Filter('single_template_hierarchy')]
    #[Filter('404_template_hierarchy')]
    public function addTemplates($templates) {
        $this->templates = array_merge($this->templates, $templates);
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
