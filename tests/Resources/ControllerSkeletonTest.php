<?php

namespace Simply\Mvc\Tests\Resources;

use PHPUnit\Framework\TestCase;

class ControllerSkeletonTest extends TestCase
{
    private function render(string $namespace, string $className): string
    {
        ob_start();
        include __DIR__ . '/../../src/Resources/skeleton/controller/Controller.tpl.php';
        return (string) ob_get_clean();
    }

    public function testGeneratedControllerIsValidPhpAndReturnsAResponse(): void
    {
        $code = $this->render('App\\Controller', 'HomeController');
        $file = tempnam(sys_get_temp_dir(), 'skeleton') . '.php';
        file_put_contents($file, $code);

        exec('php -l ' . escapeshellarg($file) . ' 2>&1', $output, $exitCode);
        unlink($file);

        $this->assertSame(0, $exitCode, implode("\n", $output));
        $this->assertStringContainsString('use Symfony\Component\HttpFoundation\Response;', $code);
        $this->assertStringContainsString('public function index(): Response', $code);
        $this->assertStringContainsString('return new Response(', $code);
    }
}
