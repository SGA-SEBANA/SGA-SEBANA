<?php

use App\Modules\Bitacora\Controllers\BitacoraController;

$router->get('/bitacora', [BitacoraController:: class,'index']);
