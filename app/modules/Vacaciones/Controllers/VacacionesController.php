<?php
namespace App\Modules\Vacaciones\Controllers;

use App\Core\ControllerBase;
use App\Modules\Vacaciones\Models\VacacionesModel;
use App\Modules\Visitas\Models\Notification; 

class VacacionesController extends ControllerBase {
    
    protected $model;
    protected $notiModel;

    public function __construct() {
        $this->model = new VacacionesModel();
        $this->notiModel = new Notification();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user']['id'])) {
            header('Location: /SGA-SEBANA/public/login?error=sesion_expirada');
            exit;
        }
    }

    public function index() {
        $usuario_id = $_SESSION['user']['id'];
        $nivel_acceso = $_SESSION['user']['nivel_acceso'] ?? 0;

        // Si es jefatura, ve todas. Si es empleado, solo las suyas.
        if ($nivel_acceso >= 50) {
            $solicitudes = $this->model->obtenerTodasConUsuario();
        } else {
            $solicitudes = $this->model->obtenerPorUsuario($usuario_id);
        }
        
        // CORREGIDO: Llamada directa a 'index'
        $this->view('index', [
            'solicitudes' => $solicitudes,
            'nivel_acceso' => $nivel_acceso
        ]);
    }

    public function create() {
        // CORREGIDO: Llamada directa a 'create'
        $this->view('create', [
            'titulo' => 'Solicitar Vacaciones',
            'error' => $_GET['error'] ?? null
        ]);
    }

    // HU-GCV-02 E1: Crear solicitud
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario_id = $_SESSION['user']['id'];
            $nombre_usuario = $_SESSION['user']['nombre_completo'];
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            $motivo = trim($_POST['motivo'] ?? '');

            $id = $this->model->crearSolicitud($usuario_id, $fecha_inicio, $fecha_fin, $motivo);

            if ($id) {
                // Notificar a las jefaturas (Simulamos enviarlo al admin principal por ahora)
                // En un sistema real, buscaríamos los IDs de los usuarios con nivel >= 50
                $this->notiModel->create([
                    'usuario_id' => 1, // ID del encargado/admin
                    'titulo' => 'Nueva Solicitud de Vacaciones',
                    'mensaje' => "El usuario {$nombre_usuario} ha solicitado vacaciones.",
                    'enlace' => "/SGA-SEBANA/public/vacaciones/show/{$id}"
                ]);

                header('Location: /SGA-SEBANA/public/vacaciones?success=creada');
                exit;
            } else {
                header('Location: /SGA-SEBANA/public/vacaciones/create?error=db_error');
                exit;
            }
        }
    }

    public function show($id) {
        $solicitud = $this->model->obtenerDetallePorId($id);
        
        if (!$solicitud) {
            header('Location: /SGA-SEBANA/public/vacaciones?error=not_found');
            exit;
        }

        // CORREGIDO: Llamada directa a 'show'
        $this->view('show', [
            'solicitud' => $solicitud,
            'nivel_acceso' => $_SESSION['user']['nivel_acceso'] ?? 0,
            'usuario_actual' => $_SESSION['user']['id']
        ]);
    }

    // HU-GCV-01 E1 y E2: Aprobar o Rechazar
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validar que solo jefaturas puedan hacer esto
            if (($_SESSION['user']['nivel_acceso'] ?? 0) < 50) {
                header('Location: /SGA-SEBANA/public/vacaciones?error=no_autorizado');
                exit;
            }

            $nuevo_estado = $_POST['nuevo_estado']; // 'Aceptada' o 'Rechazada'
            $solicitud = $this->model->obtenerDetallePorId($id);
            
            if ($this->model->cambiarEstado($id, $nuevo_estado)) {
                // Notificar al empleado que su solicitud fue procesada
                $this->notiModel->create([
                    'usuario_id' => $solicitud['usuario_id'],
                    'titulo' => 'Respuesta a tu solicitud de vacaciones',
                    'mensaje' => "Tu solicitud de vacaciones ha sido {$nuevo_estado}.",
                    'enlace' => "/SGA-SEBANA/public/vacaciones/show/{$id}"
                ]);

                header('Location: /SGA-SEBANA/public/vacaciones/show/' . $id . '?success=estado_actualizado');
                exit;
            }
        }
    }

    // HU-GCV-02 E2: Cancelar solicitud (Por parte del empleado)
    public function cancelRequest($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $solicitud = $this->model->obtenerDetallePorId($id);
            
            // Solo el dueño puede cancelar
            if ($solicitud['usuario_id'] == $_SESSION['user']['id']) {
                $this->model->cambiarEstado($id, 'Cancelada');
                header('Location: /SGA-SEBANA/public/vacaciones/show/' . $id . '?success=cancelada');
                exit;
            }
        }
    }

    // HU-GCV-02 E3: Reprogramar solicitud
    public function rescheduleRequest($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fecha_inicio = $_POST['nueva_fecha_inicio'];
            $fecha_fin = $_POST['nueva_fecha_fin'];
            $motivo = trim($_POST['nuevo_motivo'] ?? '');

            if ($this->model->reprogramarSolicitud($id, $fecha_inicio, $fecha_fin, $motivo)) {
                // Notificar al admin de la reprogramación
                $this->notiModel->create([
                    'usuario_id' => 1,
                    'titulo' => 'Reprogramación de Vacaciones',
                    'mensaje' => "El usuario {$_SESSION['user']['nombre_completo']} ha reprogramado sus vacaciones.",
                    'enlace' => "/SGA-SEBANA/public/vacaciones/show/{$id}"
                ]);

                header('Location: /SGA-SEBANA/public/vacaciones/show/' . $id . '?success=reprogramada');
                exit;
            }
        }
    }
}