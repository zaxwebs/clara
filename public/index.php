<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

use Clara\core\DB;
use Clara\core\Request;
use Clara\core\Response;
use Clara\core\Router;
use DI\Container;

require_once BASE_PATH . '/vendor/autoload.php';

$appConfig = require BASE_PATH . '/config/app.php';
$dbConfig = require BASE_PATH . '/config/database.php';
$routeConfig = require BASE_PATH . '/config/routes.php';

if (str_starts_with($dbConfig['dsn'], 'sqlite:')) {
    $databasePath = substr($dbConfig['dsn'], strlen('sqlite:'));
    $databaseDir = dirname($databasePath);

    if (!is_dir($databaseDir)) {
        mkdir($databaseDir, 0755, true);
    }
}

$container = new Container();
$container->set(
    DB::class,
    new DB(
        $dbConfig['dsn'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options'] ?? [],
    ),
);

$router = $container->get(Router::class);

foreach ($routeConfig['routes'] as $route) {
    $router->add($route['method'], $route['path'], $route['handler']);
}

/** @return array{0: class-string, 1: string} */
$resolveHandler = static function (string $handler) use ($appConfig): array {
    [$controller, $action] = array_pad(explode('@', $handler, 2), 2, null);

    if ($controller === null || $controller === '' || $action === null || $action === '') {
        throw new InvalidArgumentException('Invalid route handler provided: ' . $handler);
    }

    return [$appConfig['controller_namespace'] . $controller, $action];
};

$request = $container->get(Request::class);
$response = $container->get(Response::class);

$handler = $router->dispatch($request->method(), $request->uri());

if ($handler === null) {
    $response->setStatus(404);
    $handler = $routeConfig['not_found'];
}

[$controllerClass, $action] = $resolveHandler($handler);
$controller = $container->get($controllerClass);

if (!method_exists($controller, $action)) {
    $response->setStatus(404);
    [$controllerClass, $action] = $resolveHandler($routeConfig['not_found']);
    $controller = $container->get($controllerClass);
}

$controller->{$action}();
