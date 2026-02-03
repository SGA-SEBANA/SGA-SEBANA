<?php

use App\Modules\Usuarios\Controllers\AuthController;
use App\Modules\Usuarios\Controllers\UsersController;

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/users', [UsersController::class, 'index']);
$router->get('/users/create', [UsersController::class, 'create']);
$router->post('/users', [UsersController::class, 'store']);
$router->get('/users/{id}', [UsersController::class, 'show']);
$router->get('/users/{id}/edit', [UsersController::class, 'edit']);
$router->post('/users/{id}', [UsersController::class, 'update']);
$router->post('/users/{id}/toggle', [UsersController::class, 'toggleStatus']);

$router->get('/bitacora', [UsersController::class, 'bitacora']);
