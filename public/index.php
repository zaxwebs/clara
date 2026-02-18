<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

use Clara\core\Bootstrap;
use Clara\core\DB;
use Clara\core\Route;
use Clara\core\Router;
use DI\ContainerBuilder;
use function DI\create;

require_once BASE_PATH . '/vendor/autoload.php';

$config = require BASE_PATH . '/config/app.php';
$dbConfig = $config['database'];

if (str_starts_with($dbConfig['dsn'], 'sqlite:')) {
    $sqlitePath = substr($dbConfig['dsn'], strlen('sqlite:'));
    $dir = dirname($sqlitePath);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$builder = new ContainerBuilder();
$builder->addDefinitions([
    DB::class => create(DB::class)->constructor(
        $dbConfig['dsn'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['options'],
    ),
]);

$container = $builder->build();
$router = $container->get(Router::class);
Route::setRouter($router);

require_once BASE_PATH . '/config/routes.php';

$container->get(Bootstrap::class);
