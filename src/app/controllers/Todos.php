<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\app\models\Todo;
use Clara\core\Request;
use Clara\core\Response;

class Todos
{
    public function __construct(
        private readonly Request $request,
        private readonly Response $response,
        private readonly Todo $todo,
    ) {
    }

    public function index(): void
    {
        $this->response->view('todos.index', [
            'todos' => $this->todo->all(),
        ]);
    }

    public function store(): void
    {
        $title = trim((string) $this->request->post('title'));

        if ($title !== '') {
            $this->todo->create($title);
        }

        $this->response->redirect('/todos');
    }

    public function toggle(): void
    {
        $id = (int) $this->request->post('id');

        if ($id > 0) {
            $this->todo->toggleComplete($id);
        }

        $this->response->redirect('/todos');
    }

    public function delete(): void
    {
        $id = (int) $this->request->post('id');

        if ($id > 0) {
            $this->todo->delete($id);
        }

        $this->response->redirect('/todos');
    }
}
