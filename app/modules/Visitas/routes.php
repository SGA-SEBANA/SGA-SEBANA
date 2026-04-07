<?php

use App\Modules\Visitas\Controllers\VisitRequestController;
use App\Modules\Visitas\Controllers\AdminVisitRequestController;
use App\Modules\Visitas\Controllers\NotificationController;
use App\Modules\Afiliados\Controllers\AfiliadosController;

$router->get('/afiliados/buscar', [AfiliadosController::class, 'buscarAfiliado']);

// List request
$router->get('/visit-requests', [VisitRequestController::class, 'index']);

//Form to create request
$router->get('/visit-requests/create', [VisitRequestController::class, 'createVisit']);

// Submit request
$router->post('/visit-requests/create', [VisitRequestController::class, 'createVisit']);

// Reschedule form
$router->get('/visit-requests/{id}/reschedule',[VisitRequestController::class, 'rescheduleVisit']);

// Update reschedule
$router->post('/visit-requests/{id}/reschedule',[VisitRequestController::class, 'rescheduleVisit']);

//cancel Request
$router->post('/visit-requests/{id}/cancel',[VisitRequestController::class, 'cancelVisits']);



// Admin route
$router->get('/admin/visit-requests', [AdminVisitRequestController::class, 'index']);

$router->post('/admin/visit-requests/accept/{id}', [AdminVisitRequestController::class, 'acceptVisits']);

$router->post('/admin/visit-requests/reject/{id}', [AdminVisitRequestController::class, 'rejectRequest']);

//calendar
$router->get('/admin/request-calendar',[AdminVisitRequestController::class, 'calendar']);

$router->get('/admin/visit-calendar-events', [AdminVisitRequestController::class, 'calendarEvents']);



//Notifications
$router->post('/notificaciones/read/{id}', [NotificationController::class, 'read']);

$router->post('/notificaciones/archive/{id}', [NotificationController::class, 'archive']);

$router->post('/notificaciones/read-all', [NotificationController::class, 'markAllAsRead']);
