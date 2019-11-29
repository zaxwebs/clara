<?php

// import auto-loader
require_once './vendor/autoload.php';
// import config
require_once  './src/setup/config.php';

// set up dependency injector container
// dependency injector relies on reflection api built within PHP to figure out the dependencies of various classes
$container = new \DI\Container();

// initialize router as we want to offer setting up routes via /src/setup/routes.php
$router = $container->get('Clara\core\Router');
require_once  './src/setup/routes.php';

// initialize with our front controller, this (majorly) decouples the framework from the container.
$container->get('Clara\core\Bootstrap');
