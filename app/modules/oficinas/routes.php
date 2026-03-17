<?php

use App\Modules\oficinas\Controllers\OfficeController;

$router->get('/oficinas', [OfficeController::class, 'index']);