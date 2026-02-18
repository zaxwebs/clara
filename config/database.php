<?php

declare(strict_types=1);

return [
    'dsn' => 'sqlite:' . BASE_PATH . '/ephermal/db.sqlite',
    'username' => null,
    'password' => null,
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
