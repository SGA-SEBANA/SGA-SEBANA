<?php

use App\Modules\junta_directiva\Controllers\JuntaDirectivaController;

$router->get('/junta', [JuntaDirectivaController::class, 'index']);