<?php

namespace Simply\Mvc\Controller;

use Simply\Mvc\Attribute\Route;

class SingleController extends AbstractController {
    #[Route('single')]
    public function singlePost() {
        die('in single controller single action');
    }
}
