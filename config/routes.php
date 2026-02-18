<?php

declare(strict_types=1);

use Clara\app\controllers\Home;
use Clara\app\controllers\Todos;
use Clara\core\Route;

Route::get('/', [Home::class, 'index']);

Route::get('/todos', [Todos::class, 'index']);
Route::post('/todos', [Todos::class, 'store']);
Route::post('/todos/toggle', [Todos::class, 'toggle']);
Route::post('/todos/delete', [Todos::class, 'delete']);
