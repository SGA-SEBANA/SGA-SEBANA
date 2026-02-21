<?php
use App\Modules\Categorias\Controllers\CategoriaController;

/**
 * Rutas para el Módulo de Gestión de Categorías
 */

// 1. Listado principal de categorías
$router->get('/categorias', [CategoriaController::class, 'index']);

// 2. Proceso de registro de nuevas categorías
    $router->get('/categorias/create', [CategoriaController::class, 'create']);
$router->post('/categorias/store', [CategoriaController::class, 'store']);

// 3. Proceso de edición de categorías existentes
$router->get('/categorias/{id}/edit', [CategoriaController::class, 'edit']);
$router->post('/categorias/{id}/update', [CategoriaController::class, 'update']);

// 4. Proceso de cambio de estado (Activar/Inactivar) [HU-GC-02]
$router->post('/categorias/{id}/toggle', [CategoriaController::class, 'toggle']);

// 5. Proceso de eliminación lógica o física inteligente
$router->post('/categorias/{id}/delete', [CategoriaController::class, 'delete']);

// 6. Visualización de detalles completos [HU-GC-04 - Escenario 3]
$router->get('/categorias/{id}/show', [CategoriaController::class, 'show']);