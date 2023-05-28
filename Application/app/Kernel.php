<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;

class Kernel {
    private $request;

    public function __construct() {
        $this->request = Request::createFromGlobals();
    }

    public function getRequest() {
        return $this->request;
    }
}

return new Kernel();