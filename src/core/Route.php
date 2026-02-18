<?php

declare(strict_types=1);

namespace Clara\core;

use RuntimeException;

final class Route
{
    private static ?Router $router = null;

    public static function setRouter(Router $router): void
    {
        self::$router = $router;
    }

    public static function add(string $method, string $path, mixed $handler): void
    {
        self::router()->add($method, $path, $handler);
    }

    public static function get(string $path, mixed $handler): void
    {
        self::router()->get($path, $handler);
    }

    public static function post(string $path, mixed $handler): void
    {
        self::router()->post($path, $handler);
    }

    private static function router(): Router
    {
        if (self::$router === null) {
            throw new RuntimeException('Router has not been initialized.');
        }

        return self::$router;
    }
}
