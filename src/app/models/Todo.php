<?php

declare(strict_types=1);

namespace Clara\app\models;

use PDO;

class Todo
{
    private PDO $pdo;

    public function __construct()
    {
        $dbPath = __DIR__ . '/../../../ephermal/db.sqlite';
        $dbDir = dirname($dbPath);

        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }

        $this->pdo = new PDO('sqlite:' . $dbPath, options: [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->migrate();
    }

    private function migrate(): void
    {
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS todos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                is_completed INTEGER NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ');
    }

    /** @return array<int, array{id: int, title: string, is_completed: int, created_at: string}> */
    public function all(): array
    {
        return $this->pdo->query('SELECT * FROM todos ORDER BY created_at DESC')->fetchAll();
    }

    public function create(string $title): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO todos (title) VALUES (:title)');
        $stmt->execute(['title' => $title]);
    }

    public function toggleComplete(int $id): void
    {
        $stmt = $this->pdo->prepare('UPDATE todos SET is_completed = NOT is_completed WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM todos WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
