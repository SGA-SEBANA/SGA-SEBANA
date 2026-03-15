<?php

use App\Modules\Visitas\Controllers\VisitRequestController;

use App\Modules\Visitas\Controllers\AdminVisitRequestController;

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
$router->get('/visit-requests/{id}/cancel',[VisitRequestController::class, 'cancelVisits']);



// Admin route
$router->get('/admin/visit-requests', [AdminVisitRequestController::class, 'index']);

$router->get('/admin/visit-requests/accept/{id}', [AdminVisitRequestController::class, 'acceptVisits']);

$router->get('/admin/visit-requests/reject/{id}', [AdminVisitRequestController::class, 'cancelVisits']);



//calendar
$router->get('/admin/request-calendar',[AdminVisitRequestController::class, 'calendar']);

$router->get('/admin/visit-calendar-events', [AdminVisitRequestController::class, 'calendarEvents']);