<?php
namespace App\Modules\Vacaciones\Controllers;

use App\Core\ControllerBase;
use App\Modules\Vacaciones\Models\VacacionesModel;
use App\Modules\Visitas\Models\Notification;
use App\Modules\Usuarios\Models\User;

class VacacionesController extends ControllerBase
{
    protected $model;
    protected $notiModel;

    public function __construct()
    {
        $this->model = new VacacionesModel();
        $this->notiModel = new Notification();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->getCurrentUserId()) {
            header('Location: /SGA-SEBANA/public/login?error=sesion_expirada');
            exit;
        }
    }

    private function getCurrentUserId()
    {
        if (!empty($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        if (!empty($_SESSION['user']['id'])) {
            return (int) $_SESSION['user']['id'];
        }

        return null;
    }

    private function getCurrentUserName()
    {
        if (!empty($_SESSION['user']['nombre_completo'])) {
            return (string) $_SESSION['user']['nombre_completo'];
        }

        return 'Usuario';
    }

    private function isManager()
    {
        $nivel = $_SESSION['user']['nivel_acceso'] ?? null;

        if (is_numeric($nivel)) {
            return ((int) $nivel) >= 50;
        }

        $nivel = strtolower((string) $nivel);
        return in_array($nivel, ['alto', 'total'], true);
    }

    private function isOwner(array $solicitud, $usuarioId)
    {
        if (!empty($solicitud['usuario_id']) && (int) $solicitud['usuario_id'] === (int) $usuarioId) {
            return true;
        }

        if (!empty($solicitud['afiliado_id'])) {
            $miAfiliado = $this->model->obtenerAfiliadoIdPorUsuario($usuarioId);
            return $miAfiliado && ((int) $solicitud['afiliado_id'] === (int) $miAfiliado);
        }

        return false;
    }

    private function notifyManagersNewRequest($solicitudId, $nombreUsuario)
    {
        $userModel = new User();
        $admins = $userModel->getAdmins();

        if (empty($admins)) {
            $admins = [['id' => 1]];
        }

        foreach ($admins as $admin) {
            if (empty($admin['id'])) {
                continue;
            }

            $this->notiModel->createNotification(
                (int) $admin['id'],
                'sistema',
                'vacaciones',
                'Nueva solicitud de vacaciones',
                "{$nombreUsuario} envio una solicitud de vacaciones.",
                'solicitud_vacaciones',
                (int) $solicitudId,
                "/SGA-SEBANA/public/vacaciones/show/{$solicitudId}",
                'normal'
            );
        }
    }

    public function index()
    {
        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();

        if ($esJefatura) {
            $solicitudes = $this->model->obtenerTodasConUsuario();
        } else {
            $solicitudes = $this->model->obtenerPorUsuario($usuarioId);
        }

        $this->view('index', [
            'solicitudes' => $solicitudes,
            'es_jefatura' => $esJefatura
        ]);
    }

    public function create()
    {
        $this->view('create', [
            'titulo' => 'Solicitar Vacaciones',
            'error' => $_GET['error'] ?? null,
            'error_detail' => $_SESSION['vacaciones_error_detail'] ?? null
        ]);

        unset($_SESSION['vacaciones_error_detail']);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $nombreUsuario = $this->getCurrentUserName();
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        $motivo = trim($_POST['motivo'] ?? '');

        if ($fechaInicio === '' || $fechaFin === '') {
            $this->redirect('/SGA-SEBANA/public/vacaciones/create?error=invalid_dates');
            return;
        }

        $id = $this->model->crearSolicitud($usuarioId, $fechaInicio, $fechaFin, $motivo);

        if ($id) {
            $this->notifyManagersNewRequest($id, $nombreUsuario);
            $this->redirect('/SGA-SEBANA/public/vacaciones?success=creada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->redirect('/SGA-SEBANA/public/vacaciones/create?error=db_error');
    }

    public function show($id)
    {
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $esPropietario = $this->isOwner($solicitud, $usuarioId);

        if (!$esJefatura && !$esPropietario) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=no_autorizado');
            return;
        }

        $this->view('show', [
            'solicitud' => $solicitud,
            'es_jefatura' => $esJefatura,
            'usuario_actual' => $usuarioId,
            'es_propietario' => $esPropietario
        ]);
    }

    public function updateStatus($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        if (!$this->isManager()) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=no_autorizado');
            return;
        }

        $nuevoEstado = $_POST['nuevo_estado'] ?? '';
        $usuarioId = $this->getCurrentUserId();
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        if ($this->model->cambiarEstado($id, $nuevoEstado, $usuarioId)) {
            if (!empty($solicitud['usuario_id'])) {
                $this->notiModel->createNotification(
                    (int) $solicitud['usuario_id'],
                    'sistema',
                    'vacaciones',
                    'Solicitud de vacaciones actualizada',
                    "Tu solicitud fue marcada como {$nuevoEstado}.",
                    'solicitud_vacaciones',
                    (int) $id,
                    "/SGA-SEBANA/public/vacaciones/show/{$id}",
                    'normal'
                );
            }

            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=estado_actualizado');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }

    public function cancelRequest($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        if (!$this->isOwner($solicitud, $usuarioId)) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=no_autorizado');
            return;
        }

        if ($this->model->cambiarEstado($id, 'Cancelada', $usuarioId)) {
            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=cancelada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }

    public function rescheduleRequest($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        if (!$this->isOwner($solicitud, $usuarioId)) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=no_autorizado');
            return;
        }

        $fechaInicio = $_POST['nueva_fecha_inicio'] ?? '';
        $fechaFin = $_POST['nueva_fecha_fin'] ?? '';
        $motivo = trim($_POST['nuevo_motivo'] ?? '');

        if ($fechaInicio === '' || $fechaFin === '') {
            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=invalid_dates');
            return;
        }

        if ($this->model->reprogramarSolicitud($id, $fechaInicio, $fechaFin, $motivo)) {
            $this->notifyManagersNewRequest($id, $this->getCurrentUserName());
            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=reprogramada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }
}
