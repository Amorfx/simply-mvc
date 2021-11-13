<?php

namespace Simply\Mvc;

use Simply\Core\Query\SimplyQuery;

class Request extends \Symfony\Component\HttpFoundation\Request {
    private SimplyQuery $simplyQuery;

    public function getSimplyQuery(): SimplyQuery {
        return $this->simplyQuery;
    }

    public function setSimplyQuery(SimplyQuery $simplyQuery) {
        $this->simplyQuery = $simplyQuery;
    }
}
