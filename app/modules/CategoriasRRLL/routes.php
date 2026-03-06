<?php

use App\Modules\CategoriasRRLL\Controllers\CategoriasRRLLController;

$router->get('/CategoriasRRLL', [CategoriasRRLLController::class, 'index']);
$router->get('/CategoriasRRLL/create', [CategoriasRRLLController::class, 'create']);
$router->post('/CategoriasRRLL/store', [CategoriasRRLLController::class, 'store']);
$router->get('/CategoriasRRLL/{id}/edit', [CategoriasRRLLController::class, 'edit']);
$router->post('/CategoriasRRLL/{id}/update', [CategoriasRRLLController::class, 'update']);
$router->post('/CategoriasRRLL/{id}/delete', [CategoriasRRLLController::class, 'delete']);
$router->get('/CategoriasRRLL/{id}/show', [CategoriasRRLLController::class, 'show']);
