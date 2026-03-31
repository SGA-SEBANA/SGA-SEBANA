<?php
use App\Modules\Vacaciones\Controllers\VacacionesController;

/**
 * Rutas para el Módulo de Gestión y Control de Vacaciones (SGA-SEBANA)
 */

// Mostrar el listado principal (Panel de usuario o admin dependiendo del rol)
$router->get('/vacaciones', [VacacionesController::class, 'index']);

// Mostrar el formulario para solicitar vacaciones
$router->get('/vacaciones/create', [VacacionesController::class, 'create']);

// Recibir los datos y crear la solicitud (HU-GCV-02 E1)
$router->post('/vacaciones/store', [VacacionesController::class, 'store']);

// Mostrar el detalle de una solicitud
$router->get('/vacaciones/show/(\d+)', [VacacionesController::class, 'show']);

// Acciones de administración: Aceptar o Rechazar (HU-GCV-01 E1 y E2)
$router->post('/vacaciones/status/(\d+)', [VacacionesController::class, 'updateStatus']);

// Solicitar cancelación por parte del usuario (HU-GCV-02 E2)
$router->post('/vacaciones/cancel/(\d+)', [VacacionesController::class, 'cancelRequest']);

// Reprogramar vacaciones (HU-GCV-02 E3)
$router->post('/vacaciones/reschedule/(\d+)', [VacacionesController::class, 'rescheduleRequest']);