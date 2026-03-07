<?php

use App\Modules\CategoriasRRLL\Controllers\CategoriasRRLLController;

$router->get('/CategoriasRRLL', [CategoriasRRLLController::class, 'index']);

// Crear nueva categoría
$router->get('/CategoriasRRLL/create', [CategoriasRRLLController::class, 'create']);
$router->post('/CategoriasRRLL/store', [CategoriasRRLLController::class, 'store']);

// Editar categoría existente
$router->get('/CategoriasRRLL/{id}/edit', [CategoriasRRLLController::class, 'edit']);
$router->post('/CategoriasRRLL/{id}/update', [CategoriasRRLLController::class, 'update']);

// Eliminar categoría (validación de uso en etapas/casos)
$router->post('/CategoriasRRLL/{id}/delete', [CategoriasRRLLController::class, 'delete']);

// Cambiar estado (activo ↔ inactivo)
$router->post('/CategoriasRRLL/{id}/toggleEstado', [CategoriasRRLLController::class, 'toggleEstado']);

$router->get('/CategoriasRRLL/{id}/show', [CategoriasRRLLController::class, 'show']);



