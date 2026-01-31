<?php

use App\Modules\JuntaDirectiva\Controllers\JuntaDirectivaController;

$router->get('/junta', [JuntaDirectivaController::class, 'index']);

$router->get('/junta/create', [JuntaDirectivaController::class, 'create']);
$router->post('/junta/create', [JuntaDirectivaController::class, 'create']);

$router->get('/junta/edit/{id}', [JuntaDirectivaController::class, 'edit']);
$router->post('/junta/edit/{id}', [JuntaDirectivaController::class, 'edit']);


