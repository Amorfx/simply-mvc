<?php

namespace Simply\Mvc\Attribute;

use Attribute;

#[Attribute]
class Route {
    public function __construct(
        private string $wpCondition,
        private array $methods = array('GET', 'POST'),
        private string|false $customCondition = false) {}

    /**
     * @return string
     */
    public function getWpCondition(): string {
        return $this->wpCondition;
    }

    /**
     * @return string[]
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * @return string|false
     */
    public function getCustomCondition(): string|false {
        return $this->customCondition;
    }
}
