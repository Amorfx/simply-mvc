<?php

namespace Simply\Mvc\Routing;

class Route {
    /**
     * WordPress template condition like single, front, home, category etc.
     * @var string
     */
    protected string $wordpressCondition;

    /**
     * HTTP methods
     * @var array
     */
    protected array $methods;

    /**
     * The controller classname to retrieve action
     * @var string
     */
    protected string $controller;

    /**
     * The controller action to call
     * @var string
     */
    protected string $action;

    /**
     * Can be custom condition function or closure
     * @var object|string|bool
     */
    protected string|object|bool $customCondition = false;

    /**
     * @param string $wordpressCondition
     * @param array $methods
     * @param string $controller
     * @param string $action
     */
    public function __construct(string $wordpressCondition, array $methods, string $controller, string $action) {
        $this->wordpressCondition = $wordpressCondition;
        $this->methods = $methods;
        $this->controller = $controller;
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getWordpressCondition(): string {
        return $this->wordpressCondition;
    }

    /**
     * @param string $wordpressCondition
     */
    public function setWordpressCondition(string $wordpressCondition): void {
        $this->wordpressCondition = $wordpressCondition;
    }

    /**
     * @return array
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * @param array $methods
     */
    public function setMethods(array $methods): void {
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getController(): string {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getAction(): string {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void {
        $this->action = $action;
    }

    /**
     * @return object|string
     */
    public function getCustomCondition(): object|string {
        return $this->customCondition;
    }

    /**
     * @param object|string $customCondition
     */
    public function setCustomCondition(object|string $customCondition): void {
        $this->customCondition = $customCondition;
    }
}
