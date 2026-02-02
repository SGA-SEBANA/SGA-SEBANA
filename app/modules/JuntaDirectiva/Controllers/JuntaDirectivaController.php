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

   
   public function history(){
      $model = new JuntaDirectivaModel();
      $historial = $model->gethistorial();
      require BASE_PATH . '/app/modules/JuntaDirectiva/View/history.php';

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
      require BASE_PATH . '/app/modules/juntaDirectiva/view/create.php';
      }





   public function edit($id)
      {
         $model = new JuntaDirectivaModel();

         if($_POST){
            $model->updateMiembroJunta(
               $id,
               $_POST['cargo'],
               $_POST['fecha_inicio'],
               $_POST['fecha_fin'],
               $_POST['periodo'],
               $_POST['estado'],
               $_POST['responsabilidades'],
               $_POST['documentos'],
               $_POST['observaciones']
            );

            header("Location: /SGA-SEBANA/public/junta");
            exit;
         }
           $miembro = $model->getMiembroById($id);
           require BASE_PATH . '/app/modules/juntaDirectiva/view/edit.php';      
      }



   public function finalizar($id){
      $modelo = new JuntaDirectivaModel();
      $modelo->updateEstadoFinalizar($id, 'finalizado');

      header("Location: /SGA-SEBANA/public/junta");
      exit;
      }


   public function activar($id){
      $modelo = new JuntaDirectivaModel();
      $modelo->updateEstadoActivar($id, 'vigente');

      header("Location: /SGA-SEBANA/public/junta/history");
      exit;
      }

}