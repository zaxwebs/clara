<?php

declare(strict_types=1);

$router->get('/', 'Home@index');

$router->get('/todos', 'Todos@index');
$router->post('/todos', 'Todos@store');
$router->post('/todos/toggle', 'Todos@toggle');
$router->post('/todos/delete', 'Todos@delete');
