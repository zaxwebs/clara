<?php

declare(strict_types=1);

namespace Clara\core;

use PDO;
use PDOStatement;

class DB extends PDO
{
    public function __construct(
        string $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR,
        string $user = DB_USER,
        string $password = DB_PASS,
        array $options = [],
    ) {
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        parent::__construct($dsn, $user, $password, array_merge($defaultOptions, $options));
    }

    public function run(string $sql, array $args = []): PDOStatement|false
    {
        if ($args === []) {
            return $this->query($sql);
        }

        $query = $this->prepare($sql);
        $query->execute($args);

        return $query;
    }
}
