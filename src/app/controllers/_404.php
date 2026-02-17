<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\core\Controller;

class _404 extends Controller
{
    public function index(): void
    {
        $this->setStatus(404);
        $this->view('_404.index');
    }
}
