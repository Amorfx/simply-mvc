<?php

namespace Simply\Mvc;

use Simply\Core\Template\TemplateEngine;
use Simply\Mvc\Controller\AbstractController;
use Simply\Mvc\Routing\WordPressRouteMatcher;

class BasicController extends AbstractController{
    public function home(WordPressRouteMatcher $matcher) {
        die('home');
    }
}
