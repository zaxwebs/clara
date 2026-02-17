<?php

declare(strict_types=1);

namespace Clara\core;

use PDO;
use PDOStatement;

class DB extends PDO
{
    public function __construct()
    {
        $config = DB_CONFIG;

        if ($config['driver'] === 'sqlite') {
            $dir = dirname(str_replace('sqlite:', '', $config['dsn']));

            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        $defaultOptions = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        parent::__construct(
            $config['dsn'],
            $config['username'],
            $config['password'],
            $defaultOptions,
        );
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
