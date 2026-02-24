<?php

use App\Modules\Bitacora\Controllers\BitacoraController;

$router->get('/bitacora', [BitacoraController:: class,'index']);
$router->get('/bitacora/detalles/{id}', [BitacoraController::class,'detalles']);
$router->get('/bitacora/exportarExcel', [BitacoraController::class,'exportarExcel']); 