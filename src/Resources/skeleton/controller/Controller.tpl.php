<?= "<?php\n"; ?>

namespace <?= $namespace ?>;

use Simply\Mvc\Attribute\Route;
use Simply\Mvc\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class <?= $className ?> extends AbstractController {
    /**
     * Declare when this action answers, e.g. #[Route('single')] or #[Route('page', customCondition: 'my_condition')]
     * and render a Twig view of your theme with: return $this->render('index.html.twig');
     */
    public function index(): Response {
        return new Response('<?= $className ?>::index');
    }
}
