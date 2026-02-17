<?php

declare(strict_types=1);

namespace Clara\core;

final class Bootstrap
{
    public function __construct(
        private readonly Router $router,
        private readonly Response $response,
    ) {
        $this->router->dispatch();
    }
}
