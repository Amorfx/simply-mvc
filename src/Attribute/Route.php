<?php

namespace Simply\Mvc\Attribute;

use Attribute;

#[Attribute]
class Route {
    public function __construct(
        private string $wpCondition,
        private array $methods = array('GET', 'POST'),
        private string $customCondition = '') {}

    /**
     * @return string
     */
    public function getWpCondition(): string {
        return $this->wpCondition;
    }

    /**
     * @return array|string[]
     */
    public function getMethods(): array {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getCustomCondition(): string {
        return $this->customCondition;
    }
}
