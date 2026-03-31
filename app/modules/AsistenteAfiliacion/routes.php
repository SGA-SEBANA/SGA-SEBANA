<?php

use App\Modules\AsistenteAfiliacion\Controllers\AsistenteAfiliacionController;

// Acceso publico al formulario de afiliacion
$router->get('/afiliarse', [AsistenteAfiliacionController::class, 'create']);
$router->get('/asistente-afiliacion', [AsistenteAfiliacionController::class, 'create']);

// Flujo publico: generar/descargar PDF y enviar solicitud
$router->post('/afiliarse/pdf/generar', [AsistenteAfiliacionController::class, 'generatePdf']);
$router->post('/afiliarse/pdf/descargar', [AsistenteAfiliacionController::class, 'downloadPdf']);
$router->post('/afiliarse/enviar', [AsistenteAfiliacionController::class, 'submit']);

// Panel interno de seguimiento (requiere autenticacion)
$router->get('/asistente-afiliacion/solicitudes', [AsistenteAfiliacionController::class, 'index']);
$router->get('/asistente-afiliacion/solicitudes/{id}', [AsistenteAfiliacionController::class, 'show']);
$router->post('/asistente-afiliacion/solicitudes/{id}/estado', [AsistenteAfiliacionController::class, 'updateStatus']);
$router->get('/asistente-afiliacion/documento/{id}/{tipo}', [AsistenteAfiliacionController::class, 'documento']);
