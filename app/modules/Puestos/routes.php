<?php

use App\Modules\Puestos\Controllers\PuestosController;

// =====================================================
// PUESTOS ROUTES
// =====================================================

// Listado
$router->get('/puestos', [PuestosController::class, 'index']);

// Crear
$router->get('/puestos/create', [PuestosController::class, 'create']);
$router->post('/puestos/create', [PuestosController::class, 'create']);

// Editar
$router->get('/puestos/edit/{id}', [PuestosController::class, 'edit']);
$router->post('/puestos/edit/{id}', [PuestosController::class, 'edit']);

// Eliminar
$router->get('/puestos/delete/{id}', [PuestosController::class, 'delete']);

// Toggle estado
$router->post('/puestos/toggle/{id}', [PuestosController::class, 'toggle']);

// Historial por afiliado
$router->get('/puestos/historial/{id}', [PuestosController::class, 'historial']);

// Reportes
$router->get('/puestos/reportes', [PuestosController::class, 'reportes']);

// Exportar CSV
$router->get('/puestos/export-csv', [PuestosController::class, 'exportCsv']);

// Exportar PDF
$router->get('/puestos/export-pdf', [PuestosController::class, 'exportPdf']);
