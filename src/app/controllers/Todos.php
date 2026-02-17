<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\app\models\Todo;
use Clara\core\Controller;

class Todos extends Controller
{
    public function index(): void
    {
        $todo = new Todo();

        $this->view('todos.index', [
            'todos' => $todo->all(),
        ]);
    }

    public function store(): void
    {
        $title = trim((string) $this->post('title'));

        if ($title !== '') {
            $todo = new Todo();
            $todo->create($title);
        }

        $this->response->redirect('/todos');
    }

    public function toggle(): void
    {
        $id = (int) $this->post('id');

        if ($id > 0) {
            $todo = new Todo();
            $todo->toggleComplete($id);
        }

        $this->response->redirect('/todos');
    }

    public function delete(): void
    {
        $id = (int) $this->post('id');

        if ($id > 0) {
            $todo = new Todo();
            $todo->delete($id);
        }

        $this->response->redirect('/todos');
    }
}
