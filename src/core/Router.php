<?php

declare(strict_types=1);

namespace Clara\core;

use DI\Container;

final class Router
{
    private const NOT_FOUND_HANDLER = '_404@index';

    /** @var array<int, array{method: string, path: string, handler: string}> */
    private array $routes = [];

    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
        private readonly Container $container,
    ) {
    }

    public function add(string $method, string $path, string $handler): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $this->normalizePath($path),
            'handler' => $handler,
        ];
    }

    public function get(string $path, string $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, string $handler): void
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
            [$controller, $action] = $this->resolveHandler(self::NOT_FOUND_HANDLER);
            $invoked = $this->container->get($controller);
            $this->response->setStatus(404);
        }

        $invoked->{$action}();
    }

    /** @return array{method: string, path: string, handler: string}|null */
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
    private function resolveHandler(string $handler): array
    {
        [$controller, $action] = array_pad(explode('@', $handler, 2), 2, null);

        if ($controller === null || $controller === '' || $action === null || $action === '') {
            return ['\\Clara\\app\\controllers\\_404', 'index'];
        }

        return ['\\Clara\\app\\controllers\\' . $controller, $action];
    }
}
