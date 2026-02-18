<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\core\Response;

class _404
{
    public function __construct(private readonly Response $response)
    {
    }

    public function index(): void
    {
        $this->response->setStatus(404);
        $this->response->view('_404.index');
    }
}
