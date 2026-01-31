<?php

namespace App\Modules\JuntaDirectiva\Controllers;
use App\Modules\JuntaDirectiva\Models\JuntaDirectivaModel;

class JuntaDirectivaController
{



   public function index()
   {
      $model = new JuntaDirectivaModel();
      $junta = $model->getJuntaDirectiva();

      require BASE_PATH . '/app/modules/JuntaDirectiva/View/index.php';
   }



   public function create()
   {

      $model = new JuntaDirectivaModel();

      if($_POST){

      $model->createMiembroJunta(
         $_POST['afiliado_id'],
         $_POST['cargo'],
         $_POST['fecha_inicio'],
         $_POST['fecha_fin'],
         $_POST['periodo'],
         $_POST['estado'],
         $_POST['responsabilidades'],
         $_POST['documentos'],
         $_POST['observaciones'],
         $_POST['fecha_creacion'],
         $_POST['fecha_actualizacion']
              
      );

      header("Location: /SGA-SEBANA/public/junta");
      exit;
      }

      $afiliados = $model->getAfiliados();
      require BASE_PATH . '/app/modules/juntaDirectiva/view/createMiembroJunta.php';
      }




}