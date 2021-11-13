<?php

namespace Simply\Mvc\Hook;

use Simply;
use Simply\Core\Attributes\Filter;
use Simply\Core\Query\SimplyQuery;
use Simply\Mvc\Application;
use Simply\Mvc\Request;
use Simply\Mvc\Routing\WordPressRouteMatcher;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Simply\Mvc\Routing\Router;

class TemplateInclude implements ServiceSubscriberInterface {
    use ServiceSubscriberTrait;

    public static function getSubscribedServices() {
        return array(
            Router::class,
            WordPressRouteMatcher::class
        );
    }

    #[Filter('template_include')]
    public function sendResponse($template) {
        $request = Request::createFromGlobals();
        $request->setSimplyQuery(SimplyQuery::getCurrentQuery());
        $application = new Application(Simply::getContainer(), $request);
        $argumentResolver = new ArgumentResolver(null, array(new ArgumentResolver\ServiceValueResolver(Simply::getContainer())));
        foreach ($this->getRouter()->getAll() as $r) {
            if ($this->getMatcher()->match($r, $request)) {
                // TODO get with container
                $controller = $r->getController();
                $controller = Simply::get($controller);
                $request->attributes->set('_controller', array($controller::class, $r->getAction()));
                $arguments = $argumentResolver->getArguments($request, array($controller, $r->getAction()));
                var_dump($arguments); die;
                $response = call_user_func_array(array($controller, $r->getAction()), array($application->getRequest()));
                $response->send();
                return false;
            }
        }
        return $template;
    }

    public function getRouter(): Router {
        return $this->container->get(Router::class);
    }

    public function getMatcher(): WordPressRouteMatcher {
        return $this->container->get(WordPressRouteMatcher::class);
    }
}
