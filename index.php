<?php

// import auto-loader
require_once './vendor/autoload.php';
// import config
require_once  './src/config.php';

// set up dependency injector container
// dependency injector relies on reflection api built within PHP to figure out the dependencies of various classes
$container = new DI\Container();
// initialize with our front controller, this decouples the framework from the container.
$container->get('Clara\core\Bootstrap');
