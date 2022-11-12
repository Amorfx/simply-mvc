<?php

namespace Simply\Mvc\Controller;

use Psr\Container\ContainerInterface;
use Simply\Core\Template\TemplateEngine;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

abstract class AbstractController implements ServiceSubscriberInterface {
    protected $container;

    #[Required]
    public function setContainer(ContainerInterface $container = null): ?ContainerInterface
    {
        $previous = $this->container;
        $this->container = $container;

        return $previous;
    }

    public static function getSubscribedServices(): array {
        return [
            'twig' => TemplateEngine::class,
        ];
    }

    /**
     * Returns a rendered view.
     */
    protected function renderView(string $view, array $parameters = []): string {
        if (!$this->container->has('twig')) {
            throw new \LogicException('You cannot use the "renderView" method if the Twig Bundle is not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters, false);
    }

    /**
     * Renders a view.
     */
    protected function render(string $view, array $parameters = [], Response $response = null): Response {
        $content = $this->renderView($view, $parameters);
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($content);

        return $response;
    }


    /**
     * Returns true if the service id is defined.
     */
    protected function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * Gets a container service by its id.
     *
     * @return object The service
     */
    protected function get(string $id): object
    {
        return $this->container->get($id);
    }
}
