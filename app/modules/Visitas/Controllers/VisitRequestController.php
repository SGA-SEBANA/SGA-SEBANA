<?php

namespace App\Modules\Visitas\Controllers;

use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Usuarios\Models\User;
use App\Modules\Visitas\Models\VisitRequest;
use App\Modules\Visitas\Models\Notification;
use App\Helpers\Paginator;

class VisitRequestController {

    private function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }


    private function getCurrentUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }


    private function getCurrentAfiliadoId(VisitRequest $model): ?int
    {
        $userId = $this->getCurrentUserId();
        if (!$userId) {
            return null;
        }
        return $model->resolveAfiliadoIdByUser($userId);
    }


    private function isOwner(array $solicitud, int $afiliadoId): bool
    {
        return !empty($solicitud['afiliado_id']) && (int) $solicitud['afiliado_id'] === $afiliadoId;
    }


    private function isManager(): bool
    {
        return AccessControl::hasLevel('alto');
    }

    private function validateCsrfOrRedirect(string $redirect): bool
    {
        if (SecurityHelper::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            return true;
        }

        $_SESSION['error'] = 'Token CSRF invalido o expirado. Recargue la pagina e intente nuevamente.';
        header('Location: ' . $redirect);
        exit;
    }

    private function logBitacora(array $data): void
    {
        try {
            $bitacora = new Bitacora();
            $bitacora->log($data);
        } catch (\Throwable $e) {
            // No bloquear por bitacora.
        }
    }

    private function notifyAdminsVisitEvent(int $solicitudId, string $titulo, string $mensaje): void
    {
        $admins = (new User())->getAdmins();
        $notification = new Notification();

        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId <= 0) {
                continue;
            }

            $notification->createNotification(
                $adminId,
                'sistema',
                'visitas',
                $titulo,
                $mensaje,
                'visita',
                $solicitudId,
                '/SGA-SEBANA/public/admin/visit-requests'
            );
        }
    }

public function index()
{
    $this->ensureSession();

    $model = new VisitRequest();

    $esJefatura = $this->isManager();
    $es_jefatura = $esJefatura;

    $filtros = [];

        if (!$esJefatura) {

            $afiliadoId = $this->getCurrentAfiliadoId($model);

            if (!$afiliadoId) {
                $_SESSION['error'] = 'No fue posible asociar su usuario a un afiliado.';
                header('Location: /SGA-SEBANA/public/visit-requests?error=no_autorizado');
                exit;
            }

        $filtros['afiliado_id'] = $afiliadoId;
    }

    $pagination = Paginator::make(
        $model,
        'getVisits',
        $filtros,
        $_GET['page'] ?? 1,
        10
    );

    $solicitud = $pagination['data'];

    $page = $pagination['page'];

    $totalPaginas = $pagination['totalPaginas'];

    require BASE_PATH . '/app/modules/Visitas/Views/index.php';
}

    public function createVisit() {
        $this->ensureSession();
        $model = new VisitRequest();
        $esJefatura = $this->isManager();
        $es_jefatura = $esJefatura;
        $afiliado_id = $esJefatura ? null : $this->getCurrentAfiliadoId($model);

        if (!$esJefatura && !$afiliado_id) {
            $_SESSION['error'] = 'Debe iniciar sesion con un perfil afiliado valido.';
            header('Location: /SGA-SEBANA/public/login');
            exit;
        }

        if ($_POST) {
            $this->validateCsrfOrRedirect('/SGA-SEBANA/public/visit-requests/create');

            if ($esJefatura) {
                $afiliado_id = !empty($_POST['afiliado_id']) ? (int) $_POST['afiliado_id'] : null;
                if (empty($afiliado_id)) {
                    $_SESSION['error'] = 'Debe seleccionar un afiliado para registrar la solicitud.';
                    header('Location: /SGA-SEBANA/public/visit-requests/create');
                    exit;
                }
            }

            if (!$afiliado_id) {
                $_SESSION['error'] = 'Debe iniciar sesion con un perfil afiliado valido.';
                header('Location: /SGA-SEBANA/public/login');
                exit;
            }

            $numeroEmpleado = trim((string) ($_POST['numero_empleado'] ?? ''));
            $nombreEmpleado = trim((string) ($_POST['nombre_empleado'] ?? ''));

            if (!$esJefatura) {
                $afiliado = $model->getAfiliadoById((int) $afiliado_id);
                if (!$afiliado) {
                    $_SESSION['error'] = 'No se pudo cargar la informacion del afiliado logueado.';
                    header('Location: /SGA-SEBANA/public/visit-requests/create');
                    exit;
                }

                $numeroEmpleado = trim((string) ($afiliado['cedula'] ?? ''));
                $nombreEmpleado = trim((string) ($afiliado['nombre_completo'] ?? ''));
            } else {
                if ($numeroEmpleado === '' || $nombreEmpleado === '') {
                    $afiliado = $model->getAfiliadoById((int) $afiliado_id);
                    if ($afiliado) {
                        $numeroEmpleado = $numeroEmpleado !== '' ? $numeroEmpleado : trim((string) ($afiliado['cedula'] ?? ''));
                        $nombreEmpleado = $nombreEmpleado !== '' ? $nombreEmpleado : trim((string) ($afiliado['nombre_completo'] ?? ''));
                    }
                }
            }

            $estado = 'pendiente';
            $fecha_creacion = date('Y-m-d H:i:s');
            $fecha_actualizacion = date('Y-m-d H:i:s');

            $solicitudId = $model->createVisits(
                $afiliado_id,
                $_POST['oficina_id'] ?? null,
                $numeroEmpleado !== '' ? $numeroEmpleado : null,
                $nombreEmpleado !== '' ? $nombreEmpleado : null,
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

            if ($solicitudId) {
                $this->logBitacora([
                    'accion' => 'CREATE',
                    'modulo' => 'visitas',
                    'entidad' => 'solicitud_visita',
                    'entidad_id' => (int) $solicitudId,
                    'descripcion' => 'Creacion de solicitud de visita',
                    'datos_nuevos' => [
                        'afiliado_id' => $afiliado_id,
                        'oficina_id' => $_POST['oficina_id'] ?? null,
                        'fecha_visita' => $_POST['fecha_visita'] ?? null
                    ],
                    'resultado' => 'exitoso'
                ]);

                $nombre = $nombreEmpleado !== '' ? $nombreEmpleado : 'Empleado';
                $fecha = $_POST['fecha_visita'] ?? 'fecha no especificada';

                $titulo = 'Nueva solicitud de visita';
                $mensaje = "Solicitud de {$nombre} para el {$fecha}";
                $this->notifyAdminsVisitEvent((int) $solicitudId, $titulo, $mensaje);
            }

            header('Location: /SGA-SEBANA/public/visit-requests');
            exit;
        }

        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        $oficinas = $model->getOffices();
        $afiliados = $esJefatura ? $model->getActiveAfiliados() : [];

        $afiliadoData = null;

        if(!$es_jefatura && $afiliado_id) {
            $afiliadoData = $model->getAfiliadoById($afiliado_id);
            if (!$afiliadoData) {
                $_SESSION['error'] = 'No se pudo cargar su informacion de afiliado.';
                header('Location: /SGA-SEBANA/public/visit-requests');
                exit;
            }
        }

        require BASE_PATH . '/app/modules/Visitas/Views/submit_request.php';
    }

    public function rescheduleVisit($id) {
        $this->ensureSession();
        $model = new VisitRequest();

        $afiliadoId = $this->getCurrentAfiliadoId($model);
        if (!$afiliadoId) {
            header('Location: /SGA-SEBANA/public/visit-requests?error=no_autorizado');
            exit;
        }

        $solicitud = $model->getVisitById($id);
        if (!$solicitud || !$this->isOwner($solicitud, $afiliadoId)) {
            header('Location: /SGA-SEBANA/public/visit-requests?error=no_autorizado');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->validateCsrfOrRedirect('/SGA-SEBANA/public/visit-requests/' . (int) $id . '/reschedule');

            $fecha = $_POST['fecha_reprogramada'] ?? null;
            $hora = $_POST['hora_reprogramada'] ?? null;
            $motivo = $_POST['motivo_reprogramacion'] ?? null;

            if ($fecha && $hora && $motivo) {
                $model->rescheduleVisit($id, $fecha, $hora, $motivo);
                $this->logBitacora([
                    'accion' => 'RESCHEDULE',
                    'modulo' => 'visitas',
                    'entidad' => 'solicitud_visita',
                    'entidad_id' => (int) $id,
                    'descripcion' => 'Reprogramacion de solicitud de visita',
                    'datos_nuevos' => [
                        'fecha_reprogramada' => $fecha,
                        'hora_reprogramada' => $hora
                    ],
                    'resultado' => 'exitoso'
                ]);

                $nombreEmpleado = $solicitud['nombre_empleado'] ?? 'Empleado';
                $titulo = 'Solicitud reprogramada';
                $mensaje = "La solicitud de {$nombreEmpleado} fue reprogramada para el {$fecha}";
                $this->notifyAdminsVisitEvent((int) $id, $titulo, $mensaje);

                header('Location: /SGA-SEBANA/public/visit-requests');
                exit;
            }

            echo 'Todos los campos son obligatorios.';
            return;
        }

        include BASE_PATH . '/app/modules/Visitas/Views/reschedule.php';
    }

    public function cancelVisits($id)
    {
        $this->ensureSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /SGA-SEBANA/public/visit-requests');
            exit;
        }

        $this->validateCsrfOrRedirect('/SGA-SEBANA/public/visit-requests');

        $model = new VisitRequest();

        $afiliadoId = $this->getCurrentAfiliadoId($model);
        if (!$afiliadoId) {
            header('Location: /SGA-SEBANA/public/visit-requests?error=no_autorizado');
            exit;
        }

        $solicitud = $model->getVisitById($id);
        if (!$solicitud || !$this->isOwner($solicitud, $afiliadoId)) {
            header('Location: /SGA-SEBANA/public/visit-requests?error=no_autorizado');
            exit;
        }

        $model->updateEstado($id, 'cancelada');
        $this->logBitacora([
            'accion' => 'CANCEL',
            'modulo' => 'visitas',
            'entidad' => 'solicitud_visita',
            'entidad_id' => (int) $id,
            'descripcion' => 'Cancelacion de solicitud de visita por afiliado',
            'resultado' => 'exitoso'
        ]);
        $nombreEmpleado = $solicitud['nombre_empleado'] ?? 'Empleado';
        $this->notifyAdminsVisitEvent(
            (int) $id,
            'Solicitud de visita cancelada',
            "La solicitud de {$nombreEmpleado} fue cancelada por el afiliado."
        );

        header('Location: /SGA-SEBANA/public/visit-requests');
        exit;
    }
}
