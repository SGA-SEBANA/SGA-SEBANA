<?php
use App\Modules\Categorias\Controllers\CategoriaController;

/**
 * Rutas para el Módulo de Gestión de Categorías
 */

// 1. Listado principal de categorías
$router->get('/Categorias', [CategoriaController::class, 'index']);

// 2. Proceso de registro de nuevas categorías
    $router->get('/Categorias/create', [CategoriaController::class, 'create']);
$router->post('/Categorias/store', [CategoriaController::class, 'store']);

// 3. Proceso de edición de categorías existentes
$router->get('/Categorias/{id}/edit', [CategoriaController::class, 'edit']);
$router->post('/Categorias/{id}/update', [CategoriaController::class, 'update']);

// 4. Proceso de cambio de estado (Activar/Inactivar) [HU-GC-02]
$router->post('/Categorias/{id}/toggle', [CategoriaController::class, 'toggle']);

// 5. Proceso de eliminación lógica o física inteligente
$router->post('/Categorias/{id}/delete', [CategoriaController::class, 'delete']);

// 6. Visualización de detalles completos [HU-GC-04 - Escenario 3]
$router->get('/Categorias/{id}/show', [CategoriaController::class, 'show']);