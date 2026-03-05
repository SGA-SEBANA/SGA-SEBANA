<?php
use App\Modules\Viaticos\Controllers\ViaticoController;

/**
 * Rutas para el Módulo de Gestión y Cobro de Viáticos
 */

// Mostrar el listado principal (Data Table)
$router->get('/viaticos', [ViaticoController::class, 'index']);

// Mostrar el formulario para crear la solicitud
$router->get('/viaticos/create', [ViaticoController::class, 'create']);

// Recibir los datos del formulario y guardarlos en la base de datos
$router->post('/viaticos/store', [ViaticoController::class, 'store']);

// Mostrar los detalles de un viático específico (El Ojito)
$router->get('/viaticos/show', [ViaticoController::class, 'show']);

// Generar y descargar el PDF de la boleta
$router->get('/viaticos/pdf', [ViaticoController::class, 'generarPDF']);