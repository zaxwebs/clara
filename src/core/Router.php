<?php

declare(strict_types=1);

namespace Clara\core;

use DI\Container;

final class Router
{
    private const NOT_FOUND_HANDLER = ['\\Clara\\app\\controllers\\_404', 'index'];

    /** @var array<int, array{method: string, path: string, handler: mixed}> */
    private array $routes = [];

    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
        private readonly Container $container,
    ) {
    }

    public function add(string $method, string $path, mixed $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function get(string $path, mixed $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, mixed $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(): void
    {
        $method = $this->request->method() === 'HEAD' ? 'GET' : $this->request->method();
        $path = $this->normalizePath($this->request->path());
        $match = $this->findRoute($method, $path);

        if ($match === null) {
            $this->response->setStatus(404);
        }

        [$controller, $action] = $this->resolveHandler($match['handler'] ?? self::NOT_FOUND_HANDLER);
        $invoked = $this->container->get($controller);

        if (!method_exists($invoked, $action)) {
            [$controller, $action] = self::NOT_FOUND_HANDLER;
            $invoked = $this->container->get($controller);
            $this->response->setStatus(404);
        }

        $invoked->{$action}();
    }

    /** @return array{method: string, path: string, handler: mixed}|null */
    private function findRoute(string $method, string $path): ?array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                return $route;
            }
        }

        return null;
    }

    private function normalizePath(string $path): string
    {
        if ($path === '') {
            return '/';
        }

        return $path === '/' ? $path : rtrim($path, '/');
    }

    /** @return array{0: string, 1: string} */
    private function resolveHandler(mixed $handler): array
    {
        if (is_array($handler) && count($handler) === 2) {
            [$controller, $action] = $handler;

            if (is_string($controller) && is_string($action) && $controller !== '' && $action !== '') {
                return [$controller, $action];
            }
        }

        if (is_string($handler)) {
            [$controller, $action] = array_pad(explode('@', $handler, 2), 2, null);

            if ($controller !== null && $controller !== '' && $action !== null && $action !== '') {
                if (!str_contains($controller, '\\')) {
                    $controller = '\\Clara\\app\\controllers\\' . $controller;
                }

                return [$controller, $action];
            }
        }

        return self::NOT_FOUND_HANDLER;
    }
}
