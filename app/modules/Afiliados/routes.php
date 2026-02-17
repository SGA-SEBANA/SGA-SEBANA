<?php

use App\Modules\Afiliados\Controllers\AfiliadosController;

// =====================================================
// AFILIADOS ROUTES
// =====================================================

// Listado
$router->get('/afiliados', [AfiliadosController::class, 'index']);

// Crear
$router->get('/afiliados/create', [AfiliadosController::class, 'create']);
$router->post('/afiliados/store', [AfiliadosController::class, 'store']);

// Editar
$router->get('/afiliados/edit/{id}', [AfiliadosController::class, 'edit']);
$router->post('/afiliados/update/{id}', [AfiliadosController::class, 'update']);

// NUEVO (HU-AF-04): Cambiar Estado
$router->post('/afiliados/toggle/{id}', [AfiliadosController::class, 'toggle']);

// Vista exclusión
$router->get('/afiliados/desactivar/{id}', [AfiliadosController::class, 'desactivar']);

// Guardar exclusión
$router->post('/afiliados/procesar-baja/{id}', [AfiliadosController::class, 'procesarBaja']);
