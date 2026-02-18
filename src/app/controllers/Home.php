<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\core\Response;

class Home
{
    public function __construct(private readonly Response $response)
    {
    }

    public function index(): void
    {
        $this->response->view('home.index', [
            'message' => 'Hello World',
        ]);
    }
}
