<?php

namespace Simply\Mvc\Tests\Commands;

use PHPUnit\Framework\TestCase;
use Simply\Mvc\Commands\MakeController;

class MakeControllerTest extends TestCase
{
    /**
     * Relying on the static $defaultName property is deprecated since symfony/console 6.1 and removed in 7.0
     */
    public function testNameIsDeclaredWithoutDeprecatedStaticProperty(): void
    {
        $this->assertSame('simply:make:controller', MakeController::getDefaultName());
        $this->assertSame('Create a controller.', MakeController::getDefaultDescription());
    }
}
