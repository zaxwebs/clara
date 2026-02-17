<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\core\Controller;

class Home extends Controller
{
    public function index(): void
    {
        $this->view('home.index');
    }

    public function test(): void
    {
        $this->response->back();
    }
}
