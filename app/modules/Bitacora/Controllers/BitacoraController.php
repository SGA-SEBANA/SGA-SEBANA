<?php

namespace App\Modules\Bitacora\Controllers;
use App\Modules\Bitacora\Models\BitacoraModel;

class BitacoraController {


public function index()
{
    $model = new BitacoraModel();
    $bitacora = $model->getBitacora();
    require BASE_PATH . '/app/modules/Bitacora/Views/index.php';
}






}