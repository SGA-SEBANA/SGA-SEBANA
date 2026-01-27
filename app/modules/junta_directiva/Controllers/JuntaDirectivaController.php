<?php

namespace App\Modules\junta_directiva\Controllers;


use App\Modules\junta_directiva\Models\JuntaDirectivaModel;

class JuntaDirectivaController
{

public function index()
   {


    $model = new JuntaDirectivaModel();
    $junta = $model->getJuntaDirectiva();
  

   require BASE_PATH . '/app/modules/junta_directiva/View/index.php';
   }




}