<?php

use App\Modules\Oficinas\Controllers\OfficeController;

$router->get('/oficinas', [OfficeController::class, 'index']);
$router->get('/oficinas/create', [OfficeController::class, 'create']);
$router->post('/oficinas/create', [OfficeController::class, 'create']);
$router->get('/oficinas/edit/{id}', [OfficeController::class, 'edit']);
$router->post('/oficinas/edit/{id}', [OfficeController::class, 'edit']);
$router->get('/oficinas/toggle/{id}', [OfficeController::class, 'toggleStatus']);
$router->get('/oficinas/delete/{id}', [OfficeController::class, 'delete']);