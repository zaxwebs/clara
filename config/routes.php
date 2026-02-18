<?php

declare(strict_types=1);

return [
    'not_found' => '_404@index',
    'routes' => [
        ['method' => 'GET', 'path' => '/', 'handler' => 'Home@index'],
        ['method' => 'GET', 'path' => '/todos', 'handler' => 'Todos@index'],
        ['method' => 'POST', 'path' => '/todos', 'handler' => 'Todos@store'],
        ['method' => 'POST', 'path' => '/todos/toggle', 'handler' => 'Todos@toggle'],
        ['method' => 'POST', 'path' => '/todos/delete', 'handler' => 'Todos@delete'],
    ],
];
