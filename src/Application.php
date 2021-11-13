<?php

namespace Simply\Mvc;

use Simply\Core\Attributes\Action;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Application {
    private ContainerInterface $container;
    private Request $request;

    public function __construct(ContainerInterface $container, Request $request) {
        $this->container = $container;
        $this->request = $request;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function getContainer(): ContainerInterface {
        return $this->container;
    }

    /**
     * @return \Simply\Mvc\Request
     */
    public function getRequest(): Request {
        return $this->request;
    }
}
