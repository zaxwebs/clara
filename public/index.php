<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

use Clara\core\Bootstrap;
use Clara\core\Route;
use Clara\core\Router;
use DI\Container;

require_once BASE_PATH . '/vendor/autoload.php';
require_once BASE_PATH . '/src/setup/config.php';

$container = new Container();
$router = $container->get(Router::class);
Route::setRouter($router);

require_once BASE_PATH . '/src/setup/routes.php';

$container->get(Bootstrap::class);
