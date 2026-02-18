<?php

declare(strict_types=1);

use Clara\core\Route;

Route::get('/', 'Home@index');

Route::get('/todos', 'Todos@index');
Route::post('/todos', 'Todos@store');
Route::post('/todos/toggle', 'Todos@toggle');
Route::post('/todos/delete', 'Todos@delete');
