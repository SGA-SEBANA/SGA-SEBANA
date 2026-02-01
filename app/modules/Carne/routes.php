<?php

use App\Modules\Carne\Controllers\CarneController;

$router->get('/carnets/emitir/{id}', [CarneController::class, 'emitir']);



// Descargar PDF
$router->get('/carnets/descargar/{id}', [CarneController::class, 'descargarPdf']);

// Validar QR
$router->get('/carnets/validar/{qr}', [CarneController::class, 'validarQr']);