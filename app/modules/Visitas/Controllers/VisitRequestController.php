<?php

namespace App\Modules\Visitas\Controllers;
use App\Modules\Visitas\Models\VisitRequest;

class VisitRequestController{




public function index()
{
    $model = new VisitRequest();
    $solicitud = $model->getVisits();
    require BASE_PATH . '/app/modules/Visitas/Views/index.php';
}



public function createVisit(){
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    $model = new VisitRequest();

    if ($_POST){
        
       $afiliado_id = 1;

        if (!$afiliado_id){
            $_SESSION['error'] = "Sesión inválida.";
            header("Location: /SGA-SEBANA/public/login");
            exit;
        }
  
        $estado = "pendiente";
   
        $fecha_creacion = date('Y-m-d H:i:s');
        
        $fecha_actualizacion = date('Y-m-d H:i:s');

        $solicitudId = $model->createVisits
        (
            $afiliado_id,
            $_POST['oficina_id'] ?? null,
            $_POST['numero_empleado'] ?? null,
            $_POST['nombre_empleado'] ?? null,
            $_POST['fecha_visita'] ?? null,
            $_POST['hora_visita'] ?? null,
            $_POST['motivo'] ?? null,
            $_POST['tipo_visita'] ?? null,
            $estado,

            null, // fecha_reprogramada
            null, // hora_reprogramada
            null, // motivo_reprogramacion
            null, // motivo_cancelacion
            null, // motivo_rechazo
            null, // resultado_visita
            null, // aprobado_por
            null, // fecha_aprobacion

            $_POST['observaciones'] ?? null,

            $fecha_creacion,
            $fecha_actualizacion
        );

        header("Location: /SGA-SEBANA/public/visit-requests");
        exit;
    }

    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    require BASE_PATH . '/app/modules/Visitas/Views/submit_request.php';
}




public function rescheduleVisit($id){
    
    $model = new VisitRequest();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      
        $fecha = $_POST['fecha_reprogramada'] ?? null;
        $hora = $_POST['hora_reprogramada'] ?? null;
        $motivo = $_POST['motivo_reprogramacion'] ?? null;

        if ($fecha && $hora && $motivo) {
            $model->rescheduleVisit($id, $fecha, $hora, $motivo);
            header("Location: /SGA-SEBANA/public/visit-requests");
            exit;
        } else {
            echo "Todos los campos son obligatorios.";
        }
    } else {
     
        $solicitud = $model->getVisitById($id);
        include BASE_PATH . '/app/modules/Visitas/Views/reschedule.php';
    }
}



/*
public function delete($id){

    $model = new VisitRequest();

    $solicitud = $model->getSolicitudById($id);

    if($solicitud){

        $model->deleteSolicitud($id);

        header("Location: /SGA-SEBANA/public/visit-requests");
        exit;

    } else {

        echo "Solicitud no encontrada";

    }
}
*/

public function cancelVisits($id)
{
    $model = new VisitRequest();

    $model->updateEstado($id, 'cancelada');

      header("Location: /SGA-SEBANA/public/visit-requests");
    exit;
}




}