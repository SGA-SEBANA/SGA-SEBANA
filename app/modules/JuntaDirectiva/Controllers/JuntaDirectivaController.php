<?php

namespace App\Modules\JuntaDirectiva\Controllers;
use App\Modules\JuntaDirectiva\Models\JuntaDirectivaModel;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Visitas\Models\Notification;


class JuntaDirectivaController
{
   protected $notiModel;

   public function __construct()
    {
        $this->notiModel = new Notification();
    }



public function index()
{
    $model = new JuntaDirectivaModel();

    $pagination = \App\Helpers\Paginator::make(
        $model,
        'getJuntaDirectiva',
        [],
        $_GET['page'] ?? 1,
        10
    );

    $junta = $pagination['data'];
    $page = $pagination['page'];
    $totalPaginas = $pagination['totalPaginas'];

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

   private function cargosPermitidos(): array
   {
      return [
         'Secretaria General',
         'Subsecretaria General',
         'Secretaria de Finanzas',
         'Subsecretaria de Finanzas',
         'Secretaria de Actas y Correspondencia',
         'Secretaria de Organizacion',
         'Secretaria de Cultura y Deportes',
         'Secretaria de la Mujer',
         'Secretaria de Juventud',
         'Fiscalia',
         'Vocal 1',
         'Vocal 2',
         'Vocal 3'
      ];
   }

   private function esCargoPermitido(string $cargo): bool
   {
      return in_array($cargo, $this->cargosPermitidos(), true);
   }

   private function calcularPeriodoTrienio(string $fechaInicio): string
   {
      $timestamp = strtotime($fechaInicio);
      if ($timestamp === false) {
         return '';
      }

      $year = (int) date('Y', $timestamp);
      $base = 2026;

      if ($year < $base) {
         return $year . '-' . ($year + 3);
      }

      $inicio = $base + (int) (floor(($year - $base) / 3) * 3);
      return $inicio . '-' . ($inicio + 3);
   }


   public function create()
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
      $model = new JuntaDirectivaModel();
      $cargosDisponibles = $this->cargosPermitidos();

      if ($_POST) {
         $afiliadoId = (int) ($_POST['afiliado_id'] ?? 0);
         $cargo = trim((string) ($_POST['cargo'] ?? ''));
         $estado = strtolower(trim((string) ($_POST['estado'] ?? 'vigente')));
         $fechaInicio = trim((string) ($_POST['fecha_inicio'] ?? ''));
         $fechaFin = trim((string) ($_POST['fecha_fin'] ?? ''));
         $periodo = '';
         $responsabilidades = trim((string) ($_POST['responsabilidades'] ?? ''));
         $observaciones = trim((string) ($_POST['observaciones'] ?? ''));

         if ($afiliadoId <= 0) {
            $_SESSION['error'] = "Debe seleccionar un afiliado valido.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if ($cargo === '') {
            $_SESSION['error'] = "El campo cargo es obligatorio.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if (!$this->esCargoPermitido($cargo)) {
            $_SESSION['error'] = "Debe seleccionar un cargo valido de la lista.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if ($fechaInicio === '') {
            $_SESSION['error'] = "La fecha de inicio es obligatoria.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if ($fechaFin !== '' && $fechaFin < $fechaInicio) {
            $_SESSION['error'] = "Error: La fecha de fin no puede ser anterior a la fecha de inicio.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if ($estado === 'finalizado' && $fechaFin === '') {
            $_SESSION['error'] = "Si el estado es Finalizado, debe indicar la fecha de fin.";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         if ($this->validarCargo($cargo)) {
            $_SESSION['error'] = "Ya existe un miembro con este cargo!";
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         $periodo = $this->calcularPeriodoTrienio($fechaInicio);

         $juntaId = $model->createMiembroJunta(
            $afiliadoId,
            $cargo,
            $estado,
            $fechaInicio,
            $fechaFin === '' ? null : $fechaFin,
            $periodo === '' ? null : $periodo,
            $responsabilidades === '' ? null : $responsabilidades,
            $observaciones === '' ? null : $observaciones,
            date('Y-m-d H:i:s')
         );

         if (!$juntaId) {
            $detalle = trim((string) $model->getLastError());
            $_SESSION['error'] = 'No se pudo registrar el miembro de junta.' . ($detalle !== '' ? (' Detalle: ' . $detalle) : '');
            header("Location: /SGA-SEBANA/public/junta/create");
            exit;
         }

         // Log Bitacora
         $bitacora = new Bitacora();
         $bitacora->log([
            'accion' => 'CREATE',
            'modulo' => 'junta_directiva',
            'entidad' => 'miembro_junta',
            'entidad_id' => $juntaId,
            'descripcion' => "Registro de nuevo miembro de junta: {$cargo}",
            'datos_nuevos' => [
               'afiliado_id' => $afiliadoId,
               'cargo' => $cargo,
               'estado' => $estado,
               'fecha_inicio' => $fechaInicio,
               'fecha_fin' => $fechaFin,
               'periodo' => $periodo
            ]
         ]);

         // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'junta_directiva',
                'Nuevo Miembro de Junta',
                "Se registró el cargo {$cargo} para el afiliado ID {$afiliadoId}",
                'miembro_junta',
                $juntaId,
                "/SGA-SEBANA/public/junta/edit/{$juntaId}"
            );



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
      $cargosDisponibles = $this->cargosPermitidos();

      if ($_POST) {
         $cargo = trim((string) ($_POST['cargo'] ?? ''));
         $fechaInicio = trim((string) ($_POST['fecha_inicio'] ?? ''));
         $fechaFin = trim((string) ($_POST['fecha_fin'] ?? ''));
         $periodo = '';
         $estado = strtolower(trim((string) ($_POST['estado'] ?? 'vigente')));
         $responsabilidades = trim((string) ($_POST['responsabilidades'] ?? ''));
         $observaciones = trim((string) ($_POST['observaciones'] ?? ''));

         if (!$this->esCargoPermitido($cargo)) {
            die("Error: Debe seleccionar un cargo valido de la lista.");
         }

         if ($fechaInicio === '') {
            die("Error: La fecha de inicio es obligatoria.");
         }

         if ($fechaFin !== '' && $fechaFin < $fechaInicio) {
            die("Error: La fecha de fin no puede ser anterior a la fecha de inicio.");
         }

         if ($estado === 'finalizado' && $fechaFin === '') {
            die("Error: Si el estado es finalizado, debe indicar fecha de fin.");
         }

         $periodo = $this->calcularPeriodoTrienio($fechaInicio);

         $updated = $model->updateMiembroJunta(
            $id,
            $cargo,
            $fechaInicio,
            $fechaFin === '' ? null : $fechaFin,
            $periodo === '' ? null : $periodo,
            $estado,
            $responsabilidades === '' ? null : $responsabilidades,
            $observaciones === '' ? null : $observaciones
         );

         if (!$updated) {
            die("Error al actualizar el miembro de junta: " . $model->getLastError());
         }

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

         // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'junta_directiva',
                'Miembro de Junta Editado',
                "Se actualizaron los datos del miembro ID {$id}, cargo: {$cargo}",
                'miembro_junta',
                $id,
                "/SGA-SEBANA/public/junta/edit/{$id}"
            );






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
      if (!empty($miembro['fecha_inicio'])) {
         $miembro['periodo'] = $this->calcularPeriodoTrienio((string) $miembro['fecha_inicio']);
      }
      $documentos = $model->getDocumentos($id);
      require BASE_PATH . '/app/modules/JuntaDirectiva/View/edit.php';
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

      // Notificación
        $this->notiModel->createNotification(
            1,
            'sistema',
            'junta_directiva',
            'Cargo Finalizado',
            "Se finalizó el cargo del miembro ID {$id}",
            'miembro_junta',
            $id,
            "/SGA-SEBANA/public/junta/history"
        );


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
