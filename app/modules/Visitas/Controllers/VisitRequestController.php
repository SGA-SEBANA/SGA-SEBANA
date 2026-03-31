<?php

namespace App\Modules\Visitas\Controllers;
use App\Modules\Usuarios\Models\User;
use App\Modules\Visitas\Models\VisitRequest;
use App\Modules\Visitas\Models\Notification;

class VisitRequestController{




public function index()
{
    $model = new VisitRequest();


    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1); 
    $start = ($page - 1) * $limit;

    // Nota: si se agregan filtros a futuro
    $filtros = []; 

    $solicitud = $model->getVisits($start, $limit);

    $totalRegistros = $model->countVisits($filtros);
    $totalPaginas = ceil($totalRegistros / $limit);

  
    require BASE_PATH . '/app/modules/Visitas/Views/index.php';
}



public function createVisit(){
    if (session_status() === PHP_SESSION_NONE){
        session_start();
    }

    $model = new VisitRequest();

    if ($_POST){
        
        // Usuario en sesión
        $afiliado_id = $_SESSION['user_id'] ?? null;

        if (!$afiliado_id){
            $_SESSION['error'] = "Debe iniciar sesión.";
            header("Location: /SGA-SEBANA/public/login");
            exit;
        }

        //  Datos base
        $estado = "pendiente";
        $fecha_creacion = date('Y-m-d H:i:s'); 
        $fecha_actualizacion = date('Y-m-d H:i:s');

        $solicitudId = $model->createVisits(
            $afiliado_id,
            $_POST['oficina_id'] ?? null,
            $_POST['numero_empleado'] ?? null,
            $_POST['nombre_empleado'] ?? null,
            $_POST['fecha_visita'] ?? null,
            $_POST['hora_visita'] ?? null,
            $_POST['motivo'] ?? null,
            $_POST['tipo_visita'] ?? null,
            $estado,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $_POST['observaciones'] ?? null,
            $fecha_creacion,
            $fecha_actualizacion
        );

        //  Solo si se creo correctamente
        if ($solicitudId) {

            $userModel = new User();
            $notification = new Notification();
            
            //  Obtener admins
            $admins = $userModel->getAdmins();

            //  Mensaje 
            $nombre = $_POST['nombre_empleado'] ?? 'Empleado';
            $fecha = $_POST['fecha_visita'] ?? 'fecha no especificada';

            $titulo = "Nueva solicitud de visita";
            $mensaje = "Solicitud de {$nombre} para el {$fecha}";

            //  Crear notificacion para cada admin
            foreach ($admins as $admin) {
            $notification->createNotification(
            $admin['id'],
            'sistema',
            'visitas',
            $titulo,
            $mensaje,
            'visita',
            $solicitudId,
            '/SGA-SEBANA/public/Visitas/admin/visit-requests'
        );
        }
        }

        
        header("Location: /SGA-SEBANA/public/visit-requests");
        exit;
    }

    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['error']);
    $oficinas = $model->getOffices();

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

        
            $userModel = new \App\Modules\Usuarios\Models\User();
            $notificationModel = new \App\Modules\Visitas\Models\Notification();

            $admins = $userModel->getAdmins(); 

            $solicitud = $model->getVisitById($id);
            $nombreEmpleado = $solicitud['nombre_empleado'] ?? 'Empleado';
            $fechaNueva = $solicitud['fecha_reprogramada'] ?? $fecha;

            $titulo = "Solicitud reprogramada";
            $mensaje = "La solicitud de {$nombreEmpleado} fue reprogramada para el {$fechaNueva}";

            foreach ($admins as $admin) {
                $notificationModel->createNotification(
                    $admin['id'],      
                    'sistema',         
                    'visitas',        
                    $titulo,
                    $mensaje,
                    'visita',           
                    $id,             
                    '/SGA-SEBANA/public/admin/visit-requests'  
                );
            }

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