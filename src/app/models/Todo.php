<?php

declare(strict_types=1);

namespace Clara\app\models;

use Clara\core\DB;

class Todo
{
    public function __construct(private readonly DB $db)
    {
        $this->migrate();
    }

    private function migrate(): void
    {
        $this->db->exec('
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
        return $this->db->run('SELECT * FROM todos ORDER BY created_at DESC')->fetchAll();
    }

    public function create(string $title): void
    {
        $this->db->run('INSERT INTO todos (title) VALUES (:title)', ['title' => $title]);
    }

    public function toggleComplete(int $id): void
    {
        $this->db->run('UPDATE todos SET is_completed = NOT is_completed WHERE id = :id', ['id' => $id]);
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM todos WHERE id = :id', ['id' => $id]);
    }
}
