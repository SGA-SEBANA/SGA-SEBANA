<?php
namespace App\Modules\Vacaciones\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\Vacaciones\Models\VacacionesModel;
use App\Modules\Visitas\Models\Notification;
use App\Modules\Usuarios\Models\User;
use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Usuarios\Models\Bitacora;
use App\Helpers\Paginator;

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
        return AccessControl::hasLevel('alto');
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

    private function getSelectableAfiliados(): array
    {
        $afiliadosModel = new Afiliados();
        $rows = $afiliadosModel->getAll(['estado' => 'activo']);

        usort($rows, function ($a, $b) {
            return strcmp((string) ($a['nombre_completo'] ?? ''), (string) ($b['nombre_completo'] ?? ''));
        });

        return $rows;
    }

    private function logBitacora(array $data): void
    {
        try {
            $bitacora = new Bitacora();
            $bitacora->log($data);
        } catch (\Throwable $e) {
            // Bitacora no debe detener flujo principal.
        }
    }

    private function notifyManagersNewRequest($solicitudId, $nombreUsuario)
    {
        $this->notifyManagers(
            (int) $solicitudId,
            'Nueva solicitud de vacaciones',
            "{$nombreUsuario} envio una solicitud de vacaciones."
        );
    }

    private function notifyManagers(int $solicitudId, string $titulo, string $mensaje): void
    {
        $admins = (new User())->getAdmins();

        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId <= 0) {
                continue;
            }

            $this->notiModel->createNotification(
                $adminId,
                'sistema',
                'vacaciones',
                $titulo,
                $mensaje,
                'solicitud_vacaciones',
                (int) $solicitudId,
                "/SGA-SEBANA/public/vacaciones/show/{$solicitudId}",
                'normal'
            );
        }
    }

    private function notifyAffiliateStatus(int $solicitudId, string $estado, array $solicitud): void
    {
        $usuarioDestino = null;

        if (!empty($solicitud['afiliado_id'])) {
            $usuarioDestino = $this->model->resolveUsuarioIdPorAfiliado((int) $solicitud['afiliado_id']);
        }

        if (!$usuarioDestino && !empty($solicitud['usuario_id'])) {
            $usuarioDestino = (int) $solicitud['usuario_id'];
        }

        if (!$usuarioDestino) {
            return;
        }

        $this->notiModel->createNotification(
            $usuarioDestino,
            'sistema',
            'vacaciones',
            'Solicitud de vacaciones actualizada',
            "Tu solicitud de vacaciones #{$solicitudId} fue actualizada a estado {$estado}.",
            'solicitud_vacaciones',
            $solicitudId,
            "/SGA-SEBANA/public/vacaciones/show/{$solicitudId}",
            'normal'
        );
    }

    private function validateCsrfOrRedirect(string $redirect): void
    {
        if (SecurityHelper::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            return;
        }

        $this->redirect($redirect . (strpos($redirect, '?') === false ? '?error=csrf' : '&error=csrf'));
        exit;
    }

public function index()
{
    $usuarioId = $this->getCurrentUserId();
    $esJefatura = $this->isManager();

    $filtros = [];

    if (!$esJefatura) {
        $filtros['usuario_id'] = $usuarioId;
    }

    $pagination = Paginator::make(
        $this->model,
        'getAll',
        $filtros,
        $_GET['page'] ?? 1,
        10
    );

    $this->view('index', [
        'solicitudes' => $pagination['data'],
        'page' => $pagination['page'],
        'totalPaginas' => $pagination['totalPaginas'],
        'es_jefatura' => $esJefatura
    ]);
}

    public function create()
    {
        $esJefatura = $this->isManager();

        $this->view('create', [
            'titulo' => 'Solicitar Vacaciones',
            'error' => $_GET['error'] ?? null,
            'error_detail' => $_SESSION['vacaciones_error_detail'] ?? null,
            'es_jefatura' => $esJefatura,
            'afiliados' => $esJefatura ? $this->getSelectableAfiliados() : []
        ]);

        unset($_SESSION['vacaciones_error_detail']);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $this->validateCsrfOrRedirect('/SGA-SEBANA/public/vacaciones/create');

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $afiliadoId = null;

        if ($esJefatura) {
            $afiliadoId = !empty($_POST['afiliado_id']) ? (int) $_POST['afiliado_id'] : null;
            if (empty($afiliadoId)) {
                $_SESSION['vacaciones_error_detail'] = 'Debe seleccionar un afiliado para registrar la solicitud.';
                $this->redirect('/SGA-SEBANA/public/vacaciones/create?error=invalid_afiliado');
                return;
            }
        }

        $nombreUsuario = $this->getCurrentUserName();
        $fechaInicio = $_POST['fecha_inicio'] ?? '';
        $fechaFin = $_POST['fecha_fin'] ?? '';
        $motivo = trim($_POST['motivo'] ?? '');

        if ($fechaInicio === '' || $fechaFin === '') {
            $this->redirect('/SGA-SEBANA/public/vacaciones/create?error=invalid_dates');
            return;
        }

        $id = $this->model->crearSolicitud($usuarioId, $fechaInicio, $fechaFin, $motivo, $afiliadoId);

        if ($id) {
            $this->logBitacora([
                'accion' => 'CREATE',
                'modulo' => 'vacaciones',
                'entidad' => 'solicitud_vacaciones',
                'entidad_id' => (int) $id,
                'descripcion' => 'Creacion de solicitud de vacaciones',
                'datos_nuevos' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin,
                    'afiliado_id' => $afiliadoId
                ],
                'resultado' => 'exitoso'
            ]);
            $this->notifyManagersNewRequest($id, $nombreUsuario);
            $this->redirect('/SGA-SEBANA/public/vacaciones?success=creada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->logBitacora([
            'accion' => 'CREATE',
            'modulo' => 'vacaciones',
            'entidad' => 'solicitud_vacaciones',
            'descripcion' => 'Error al crear solicitud de vacaciones',
            'resultado' => 'fallido',
            'mensaje_error' => $this->model->getLastError()
        ]);
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

        $this->validateCsrfOrRedirect('/SGA-SEBANA/public/vacaciones/show/' . (int) $id);

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
            $this->logBitacora([
                'accion' => 'STATUS_CHANGE',
                'modulo' => 'vacaciones',
                'entidad' => 'solicitud_vacaciones',
                'entidad_id' => (int) $id,
                'descripcion' => "Cambio de estado de vacaciones a {$nuevoEstado}",
                'datos_nuevos' => ['estado' => $nuevoEstado],
                'resultado' => 'exitoso'
            ]);
            $this->notifyAffiliateStatus((int) $id, (string) $nuevoEstado, $solicitud);

            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=estado_actualizado');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->logBitacora([
            'accion' => 'STATUS_CHANGE',
            'modulo' => 'vacaciones',
            'entidad' => 'solicitud_vacaciones',
            'entidad_id' => (int) $id,
            'descripcion' => 'Error al actualizar estado de vacaciones',
            'resultado' => 'fallido',
            'mensaje_error' => $this->model->getLastError()
        ]);
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }

    public function cancelRequest($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $this->validateCsrfOrRedirect('/SGA-SEBANA/public/vacaciones/show/' . (int) $id);

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        if (!$esJefatura && !$this->isOwner($solicitud, $usuarioId)) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=no_autorizado');
            return;
        }

        if ($this->model->cambiarEstado($id, 'Cancelada', $usuarioId)) {
            $this->logBitacora([
                'accion' => 'CANCEL',
                'modulo' => 'vacaciones',
                'entidad' => 'solicitud_vacaciones',
                'entidad_id' => (int) $id,
                'descripcion' => $esJefatura
                    ? 'Cancelacion de solicitud de vacaciones por administracion'
                    : 'Cancelacion de solicitud de vacaciones por afiliado',
                'resultado' => 'exitoso'
            ]);

            if ($esJefatura) {
                $this->notifyAffiliateStatus((int) $id, 'Cancelada', $solicitud);
            } else {
                $this->notifyManagers(
                    (int) $id,
                    'Solicitud de vacaciones cancelada',
                    "{$this->getCurrentUserName()} cancelo su solicitud de vacaciones."
                );
            }

            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=cancelada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->logBitacora([
            'accion' => 'CANCEL',
            'modulo' => 'vacaciones',
            'entidad' => 'solicitud_vacaciones',
            'entidad_id' => (int) $id,
            'descripcion' => 'Error al cancelar solicitud de vacaciones',
            'resultado' => 'fallido',
            'mensaje_error' => $this->model->getLastError()
        ]);
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }

    public function rescheduleRequest($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/vacaciones');
            return;
        }

        $this->validateCsrfOrRedirect('/SGA-SEBANA/public/vacaciones/show/' . (int) $id);

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $solicitud = $this->model->obtenerDetallePorId($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/vacaciones?error=not_found');
            return;
        }

        if (!$esJefatura && !$this->isOwner($solicitud, $usuarioId)) {
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
            $this->logBitacora([
                'accion' => 'RESCHEDULE',
                'modulo' => 'vacaciones',
                'entidad' => 'solicitud_vacaciones',
                'entidad_id' => (int) $id,
                'descripcion' => $esJefatura
                    ? 'Reprogramacion de solicitud de vacaciones por administracion'
                    : 'Reprogramacion de solicitud de vacaciones por afiliado',
                'datos_nuevos' => [
                    'fecha_inicio' => $fechaInicio,
                    'fecha_fin' => $fechaFin
                ],
                'resultado' => 'exitoso'
            ]);

            if ($esJefatura) {
                $this->notifyAffiliateStatus((int) $id, 'Pendiente', $solicitud);
            } else {
                $this->notifyManagers(
                    (int) $id,
                    'Solicitud de vacaciones reprogramada',
                    "{$this->getCurrentUserName()} reprogramo su solicitud de vacaciones."
                );
            }

            $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?success=reprogramada');
            return;
        }

        $_SESSION['vacaciones_error_detail'] = $this->model->getLastError();
        $this->logBitacora([
            'accion' => 'RESCHEDULE',
            'modulo' => 'vacaciones',
            'entidad' => 'solicitud_vacaciones',
            'entidad_id' => (int) $id,
            'descripcion' => 'Error al reprogramar solicitud de vacaciones',
            'resultado' => 'fallido',
            'mensaje_error' => $this->model->getLastError()
        ]);
        $this->redirect('/SGA-SEBANA/public/vacaciones/show/' . $id . '?error=db_error');
    }
}
