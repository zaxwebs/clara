<?php

declare(strict_types=1);

namespace Clara\core;

use PDO;
use PDOStatement;

class DB extends PDO
{
    public function __construct(string $dsn, ?string $username = null, ?string $password = null, array $options = [])
    {
        parent::__construct($dsn, $username, $password, $options);
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
