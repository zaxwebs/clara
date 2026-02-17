<?php

declare(strict_types=1);

namespace Clara\core;

class Request
{
    private function search(array $array, string $key, mixed $default = null): mixed
    {
        return array_key_exists($key, $array) ? $array[$key] : $default;
    }

    public function body(?string $default = null): ?string
    {
        return file_get_contents('php://input') ?: $default;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->search($_GET, $key, $default);
    }

    public function post(string $key, mixed $default = null): mixed
    {
        return $this->search($_POST, $key, $default);
    }

    public function files(string $key, mixed $default = null): mixed
    {
        return $this->search($_FILES, $key, $default);
    }

    public function session(string $key, mixed $default = null): mixed
    {
        return $this->search($_SESSION ?? [], $key, $default);
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->search($_COOKIE, $key, $default);
    }

    public function server(string $key, mixed $default = null): mixed
    {
        return $this->search($_SERVER, $key, $default);
    }

    public function method(): string
    {
        return strtoupper((string) $this->server('REQUEST_METHOD', 'GET'));
    }

    public function uri(): string
    {
        return (string) $this->server('REQUEST_URI', '/');
    }

    public function path(): string
    {
        $path = parse_url($this->uri(), PHP_URL_PATH);

        if (!is_string($path) || $path === '') {
            return '/';
        }

        return $path === '/' ? $path : rtrim($path, '/');
    }
}
