<?php

namespace Simply\Mvc\Routing;

class Route {
    /**
     * Conditions whose template in the WordPress hierarchy has another name
     */
    private const TEMPLATE_NAMES = ['front' => 'front-page', 'tax' => 'taxonomy'];

    /**
     * WordPress template condition like single, front, home, category etc.
     * @var string
     */
    protected string $wordpressCondition;

    /**
     * HTTP methods
     * @var string[]
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
     * @param string[] $methods
     * @param string $controller
     * @param string $action
     * @param string|object|bool $customCondition
     */
    public function __construct(string $wordpressCondition, array $methods, string $controller, string $action, string|object|bool $customCondition = false) {
        $this->wordpressCondition = $wordpressCondition;
        $this->methods = $methods;
        $this->controller = $controller;
        $this->action = $action;
        $this->customCondition = $customCondition;
    }

    /**
     * @return string
     */
    public function getWordpressCondition(): string {
        return $this->wordpressCondition;
    }

    /**
     * Name of the condition in the WordPress template hierarchy
     */
    public function getTemplateName(): string {
        return self::TEMPLATE_NAMES[$this->wordpressCondition] ?? $this->wordpressCondition;
    }

    /**
     * @param string $wordpressCondition
     */
    public function setWordpressCondition(string $wordpressCondition): void {
        $this->wordpressCondition = $wordpressCondition;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * @param string[] $methods
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
     * @return object|string|bool
     */
    public function getCustomCondition(): object|string|bool {
        return $this->customCondition;
    }

    /**
     * @param object|string $customCondition
     */
    public function setCustomCondition(object|string $customCondition): void {
        $this->customCondition = $customCondition;
    }

    /**
     * Used to get instance after var export in cached container
     * @param array<string, mixed> $array
     */
    public static function __set_state(array $array): self {
        return new self(...$array);
    }
}
