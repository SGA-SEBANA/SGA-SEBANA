<?php
use App\Modules\Ayudas\Controllers\AyudaEconomicaController;

/**
 * Rutas para el Módulo de Ayudas Económicas (SEBANA)
 */

// Mostrar el listado principal (Data Table)
$router->get('/ayudas', [AyudaEconomicaController::class, 'index']);

// Mostrar el formulario para crear la solicitud (HU-SAEC-1 E1)
$router->get('/ayudas/create', [AyudaEconomicaController::class, 'create']);

// Recibir los datos del formulario y guardarlos en la base de datos (HU-SAEC-1 E1 / HU-SAEC-3)
$router->post('/ayudas/store', [AyudaEconomicaController::class, 'store']);

// Mostrar los detalles de una ayuda específica (HU-SAEC-4)
$router->get('/ayudas/show/(\d+)', [AyudaEconomicaController::class, 'show']);

// Acciones de administración: Aprobar, Rechazar o Reevaluar (HU-SAEC-2)
$router->post('/ayudas/status/(\d+)', [AyudaEconomicaController::class, 'updateStatus']);

// Solicitar cancelación por parte del usuario (HU-SAEC-1 E2)
$router->post('/ayudas/cancel/(\d+)', [AyudaEconomicaController::class, 'requestCancellation']);

// Agregar más evidencia posterior al envío (HU-SAEC-5)
$router->post('/ayudas/evidence/(\d+)', [AyudaEconomicaController::class, 'addEvidence']);

// Descargar/ver evidencia desde storage
$router->get('/ayudas/archivo/(\d+)', [AyudaEconomicaController::class, 'archivo']);
