<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

use Clara\core\Bootstrap;
use Clara\core\Router;
use DI\Container;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/setup/config.php';

$container = new Container();
$router = $container->get(Router::class);

require_once __DIR__ . '/src/setup/routes.php';

$container->get(Bootstrap::class);
