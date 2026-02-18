<?php

declare(strict_types=1);

namespace Clara\core;

final class Router
{
    /** @var array<int, array{method: string, path: string, handler: string}> */
    private array $routes = [];

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

    public function dispatch(string $method, string $uri): ?string
    {
        $normalizedMethod = strtoupper($method) === 'HEAD' ? 'GET' : strtoupper($method);
        $path = parse_url($uri, PHP_URL_PATH);
        $normalizedPath = $this->normalizePath(is_string($path) ? $path : '/');

        foreach ($this->routes as $route) {
            if ($route['method'] === $normalizedMethod && $route['path'] === $normalizedPath) {
                return $route['handler'];
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
}
