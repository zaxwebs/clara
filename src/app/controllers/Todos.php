<?php

declare(strict_types=1);

namespace Clara\app\controllers;

use Clara\app\models\Todo;
use Clara\core\Controller;
use Clara\core\DB;
use Clara\core\Request;
use Clara\core\Response;

class Todos extends Controller
{
    public function __construct(
        Request $request,
        Response $response,
        ?DB $db,
        private readonly Todo $todo,
    ) {
        parent::__construct($request, $response, $db);
    }

    public function index(): void
    {
        $this->view('todos.index', [
            'todos' => $this->todo->all(),
        ]);
    }

    public function store(): void
    {
        $title = trim((string) $this->post('title'));

        if ($title !== '') {
            $this->todo->create($title);
        }

        $this->response->redirect('/todos');
    }

    public function toggle(): void
    {
        $id = (int) $this->post('id');

        if ($id > 0) {
            $this->todo->toggleComplete($id);
        }

        $this->response->redirect('/todos');
    }

    public function delete(): void
    {
        $id = (int) $this->post('id');

        if ($id > 0) {
            $this->todo->delete($id);
        }

        $this->response->redirect('/todos');
    }
}
