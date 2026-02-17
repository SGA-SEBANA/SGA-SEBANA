<?php

namespace App\Modules\JuntaDirectiva\Controllers;
use App\Modules\JuntaDirectiva\Models\JuntaDirectivaModel;
use App\Modules\Usuarios\Models\Bitacora;

class JuntaDirectivaController
{


   public function index()
   {
      $model = new JuntaDirectivaModel();
      $junta = $model->getJuntaDirectiva();
      require BASE_PATH . '/app/modules/JuntaDirectiva/View/index.php';
   }


   public function history()
   {
      $model = new JuntaDirectivaModel();
      $historial = $model->gethistorial();
      require BASE_PATH . '/app/modules/JuntaDirectiva/View/history.php';

   }

   public function validarCargo($cargo = null){
      $model = new JuntaDirectivaModel();
      if ($cargo === null) {
         return $model->getCargosActivos();
      }
      return $model->cargoExists($cargo);
}


   public function create()
   {
      session_start();
      $model = new JuntaDirectivaModel();

      if ($_POST) {

         if (!empty($_POST['fecha_fin']) && $_POST['fecha_fin'] < $_POST['fecha_inicio']) {
            $_SESSION['error'] = "Error: La fecha de fin no puede ser anterior a la fecha de inicio.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

          if ($this->validarCargo($_POST['cargo'])) {
            $_SESSION['error'] = "Ya existe un miembro con este cargo!";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
            
         }
  

         $juntaId = $model->createMiembroJunta(
            $_POST['afiliado_id'],
            $_POST['cargo'],
            $_POST['estado'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin'],
            $_POST['periodo'],
            $_POST['responsabilidades'],
            $_POST['observaciones'],
            date('Y-m-d H:i:s')


         );

         // Log Bitacora
         $bitacora = new Bitacora();
         $bitacora->log([
            'accion' => 'CREATE',
            'modulo' => 'junta_directiva',
            'entidad' => 'miembro_junta',
            'entidad_id' => $juntaId,
            'descripcion' => "Registro de nuevo miembro de junta: {$_POST['cargo']}",
            'datos_nuevos' => $_POST
         ]);

         if (!empty($_FILES['documentos']['name'][0])) {

            foreach ($_FILES['documentos']['name'] as $i => $nombreOriginal) {

               if ($_FILES['documentos']['size'][$i] > 5 * 1024 * 1024) {
                 $_SESSION['error'] = "El archivo excede el tamaño máximo permitido (5MB).";
                 header("Location: /SGA-SEBANA/public/junta/create");
                 exit;
               }

               $finfo = finfo_open(FILEINFO_MIME_TYPE);
               $mime = finfo_file($finfo, $_FILES['documentos']['tmp_name'][$i]);

               $permitidos = ['application/pdf', 'image/jpeg', 'image/png'];

               if (!in_array($mime, $permitidos)) {
                  $_SESSION['error'] = "Tipo de archivo no permitido. Solo se permiten PDF, JPEG y PNG.";
                   header("Location: /SGA-SEBANA/public/junta/create");
                   exit;
               }

               $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
               $nombreSeguro = uniqid("doc_") . "." . $ext;

               $destino = BASE_PATH . "/storage/junta/" . $nombreSeguro;
               move_uploaded_file($_FILES['documentos']['tmp_name'][$i], $destino);

               $model->insertDocumento(
                  $juntaId,
                  $nombreSeguro,
                  $nombreOriginal
               );
            }
         }

         header("Location: /SGA-SEBANA/public/junta");
         exit;
      }

      $afiliados = $model->getAfiliados();

      $error = $_SESSION['error'] ?? null;
      unset($_SESSION['error']);
      require BASE_PATH . '/app/modules/JuntaDirectiva/View/create.php';
   }





   public function edit($id)
   {
      $model = new JuntaDirectivaModel();

      if ($_POST) {
         if (!empty($_POST['fecha_fin']) && $_POST['fecha_fin'] < $_POST['fecha_inicio']) {
            die("Error: La fecha de fin no puede ser anterior a la fecha de inicio.");
         }

         $model->updateMiembroJunta(
            $id,
            $_POST['cargo'],
            $_POST['fecha_inicio'],
            $_POST['fecha_fin'],
            $_POST['periodo'],
            $_POST['estado'],
            $_POST['responsabilidades'],
            $_POST['observaciones']
         );

         // Log Bitacora
         $bitacora = new Bitacora();
         $bitacora->log([
            'accion' => 'UPDATE',
            'modulo' => 'junta_directiva',
            'entidad' => 'miembro_junta',
            'entidad_id' => $id,
            'descripcion' => "Actualización de miembro de junta ID: {$id}",
            'datos_nuevos' => $_POST
         ]);
         if (!empty($_FILES['documentos']['name'][0])) {
            foreach ($_FILES['documentos']['name'] as $i => $nombreOriginal) {

               if ($_FILES['documentos']['size'][$i] > 5 * 1024 * 1024) {
                  die("Archivo muy grande");
               }

               $finfo = finfo_open(FILEINFO_MIME_TYPE);
               $mime = finfo_file($finfo, $_FILES['documentos']['tmp_name'][$i]);

               $permitidos = ['application/pdf', 'image/jpeg', 'image/png'];
               if (!in_array($mime, $permitidos)) {
                  die("Tipo de archivo no permitido");
               }

               $ext = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
               $nombreSeguro = uniqid("doc_") . "." . $ext;

               $destino = BASE_PATH . "/storage/junta/" . $nombreSeguro;
               move_uploaded_file($_FILES['documentos']['tmp_name'][$i], $destino);

               $model->insertDocumento($id, $nombreSeguro, $nombreOriginal);
            }
         }

         header("Location: /SGA-SEBANA/public/junta");
         exit;
      }
      $miembro = $model->getMiembroById($id);
      $documentos = $model->getDocumentos($id);
      require BASE_PATH . '/app/modules/juntaDirectiva/view/edit.php';
   }




   public function finalizar($id)
   {
      $modelo = new JuntaDirectivaModel();
      $modelo->updateEstadoFinalizar($id, 'finalizado');

      // Log Bitacora
      $bitacora = new Bitacora();
      $bitacora->log([
         'accion' => 'FINALIZAR',
         'modulo' => 'junta_directiva',
         'entidad' => 'miembro_junta',
         'entidad_id' => $id,
         'descripcion' => "Finalización de cargo para miembro ID: {$id}",
         'resultado' => 'exitoso'
      ]);

      header("Location: /SGA-SEBANA/public/junta");
      exit;
   }




   public function activar($id)
   {
      $modelo = new JuntaDirectivaModel();
      $modelo->updateEstadoActivar($id, 'vigente');

      // Log Bitacora
      $bitacora = new Bitacora();
      $bitacora->log([
         'accion' => 'ACTIVAR',
         'modulo' => 'junta_directiva',
         'entidad' => 'miembro_junta',
         'entidad_id' => $id,
         'descripcion' => "Reactivación de cargo para miembro ID: {$id}",
         'resultado' => 'exitoso'
      ]);

      header("Location: /SGA-SEBANA/public/junta/history");
      exit;
   }



   public function documentos($id)
   {
      $model = new JuntaDirectivaModel();

      $miembro = $model->getMiembroById($id);
      $documentos = $model->getDocumentos($id);

      if (!$miembro) {
         exit("Miembro no encontrado");
      }

      require BASE_PATH . '/app/modules/JuntaDirectiva/View/documentos.php';
   }



   public function verDocumento($id)
   {
      $model = new JuntaDirectivaModel();

      $doc = $model->getDocumentoById($id);

      if (!$doc) {
         exit("Documento no encontrado");
      }

      $archivo = BASE_PATH . "/storage/junta/" . $doc['nombre_archivo'];

      if (!file_exists($archivo)) {
         exit("Archivo físico no existe");
      }

      header("Content-Type: " . mime_content_type($archivo));
      header("Content-Disposition: inline; filename=\"" . $doc['nombre_original'] . "\"");
      readfile($archivo);
      exit;
   }


   public function eliminarDocumento($id)
   {
      $model = new JuntaDirectivaModel();
      $doc = $model->getDocumentoById($id);

      if (!$doc) {
         exit("No existe");
      }

      $archivo = BASE_PATH . "/storage/junta/" . $doc['nombre_archivo'];
      if (file_exists($archivo)) {
         unlink($archivo);
      }

      $model->deleteDocumento($id);

      header("location: " . $_SERVER['HTTP_REFERER']);
      exit;
   }

}