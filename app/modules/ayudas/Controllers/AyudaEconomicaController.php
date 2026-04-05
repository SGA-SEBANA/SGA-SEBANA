<?php
namespace App\Modules\Ayudas\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\Ayudas\Models\AyudaEconomicaModel;
use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Usuarios\Models\User;
use App\Modules\Visitas\Models\Notification; 

class AyudaEconomicaController extends ControllerBase {

    protected $model;
    protected $notiModel;


    public function __construct() {
        $this->model = new AyudaEconomicaModel();
        $this->notiModel = new Notification();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    private function isManager(): bool {
        return AccessControl::hasLevel('alto');
    }

    private function isOwner(array $ayuda, int $usuarioId): bool {
        if (!empty($ayuda['usuario_id']) && (int) $ayuda['usuario_id'] === $usuarioId) {
            return true;
        }

        if (!empty($ayuda['afiliado_id'])) {
            $miAfiliado = $this->model->obtenerAfiliadoIdPorUsuario($usuarioId);
            return $miAfiliado && ((int) $ayuda['afiliado_id'] === (int) $miAfiliado);
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
            // No interrumpir el flujo funcional.
        }
    }

    private function notifyAdmins(string $titulo, string $mensaje, string $entidadTipo, int $entidadId, string $url, string $categoria = 'solicitudes'): void
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
                $categoria,
                $titulo,
                $mensaje,
                $entidadTipo,
                $entidadId,
                $url
            );
        }
    }

    private function notifyAdminsNewRequest(int $ayudaId, float $montoSolicitado): void
    {
        $this->notifyAdmins(
            'Nueva solicitud de ayuda economica',
            "Se registro una nueva solicitud de ayuda economica por un monto de CRC {$montoSolicitado}.",
            'solicitud_ayuda',
            $ayudaId,
            "/SGA-SEBANA/public/ayudas/show/{$ayudaId}",
            'ayuda_economica'
        );
    }

    private function notifyAdminsCancellationRequest(int $ayudaId): void
    {
        $this->notifyAdmins(
            'Solicitud de cancelacion de ayuda',
            "Se solicito cancelar la ayuda economica #{$ayudaId}.",
            'solicitud_ayuda',
            $ayudaId,
            "/SGA-SEBANA/public/ayudas/show/{$ayudaId}",
            'ayuda_economica'
        );
    }

    private function notifyAdminsEvidence(int $ayudaId): void
    {
        $this->notifyAdmins(
            'Nueva evidencia en ayuda economica',
            "Se adjunto nueva evidencia en la ayuda economica #{$ayudaId}.",
            'solicitud_ayuda',
            $ayudaId,
            "/SGA-SEBANA/public/ayudas/show/{$ayudaId}",
            'ayuda_economica'
        );
    }

    private function notifyAffiliateStatus(int $ayudaId, string $estado, array $ayuda): void
    {
        $usuarioDestino = null;

        if (!empty($ayuda['afiliado_id'])) {
            $usuarioDestino = $this->model->resolveUsuarioIdPorAfiliado((int) $ayuda['afiliado_id']);
        }

        if (!$usuarioDestino && !empty($ayuda['usuario_id'])) {
            $usuarioDestino = (int) $ayuda['usuario_id'];
        }

        if (!$usuarioDestino) {
            return;
        }

        $this->notiModel->createNotification(
            $usuarioDestino,
            'sistema',
            'ayuda_economica',
            'Solicitud de ayuda economica actualizada',
            "Tu solicitud de ayuda economica #{$ayudaId} cambio a estado {$estado}.",
            'solicitud_ayuda',
            $ayudaId,
            "/SGA-SEBANA/public/ayudas/show/{$ayudaId}"
        );
    }

    public function index() {
        $usuarioId = $this->getCurrentUserId();
        if (!$usuarioId) {
            $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
            return;
        }

        $esJefatura = $this->isManager();
        $ayudas = $esJefatura
            ? $this->model->obtenerTodas()
            : $this->model->obtenerPorUsuario($usuarioId);

        $this->view('index', [
            'ayudas' => $ayudas,
            'es_jefatura' => $esJefatura
        ]);
    }

    public function create() {
        $esJefatura = $this->isManager();
        $this->view('create', [
            'titulo' => 'Solicitar Ayuda Economica',
            'error' => $_GET['error'] ?? null,
            'es_jefatura' => $esJefatura,
            'afiliados' => $esJefatura ? $this->getSelectableAfiliados() : []
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
                return;
            }
            $usuario_id = $_SESSION['user_id'];
            $afiliado_id = null;

            if ($this->isManager()) {
                $afiliado_id = !empty($_POST['afiliado_id']) ? (int) $_POST['afiliado_id'] : null;
                if (empty($afiliado_id)) {
                    $this->redirect('/SGA-SEBANA/public/ayudas/create?error=invalid_afiliado');
                    return;
                }
            }

            if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
                if (!$this->validarArchivo($_FILES['evidencia'])) {
                    $this->redirect('/SGA-SEBANA/public/ayudas/create?error=invalid_file');
                    return;
                }
            }

            $motivo = trim($_POST['motivo'] ?? '');
            $monto_solicitado = (float) ($_POST['monto_solicitado'] ?? 0);

            if ($monto_solicitado > 100000) {
                $this->redirect('/SGA-SEBANA/public/ayudas/create?error=monto_excedido');
                return;
            }

            $ayuda_id = $this->model->crearSolicitud($usuario_id, $motivo, $monto_solicitado, $afiliado_id);

            if ($ayuda_id) {
                unset($_SESSION['error_detail']);
                $this->logBitacora([
                    'accion' => 'CREATE',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'entidad_id' => (int) $ayuda_id,
                    'descripcion' => 'Creacion de solicitud de ayuda economica',
                    'datos_nuevos' => [
                        'afiliado_id' => $afiliado_id,
                        'monto_solicitado' => $monto_solicitado
                    ],
                    'resultado' => 'exitoso'
                ]);
                $this->notifyAdminsNewRequest((int) $ayuda_id, $monto_solicitado);

                
                if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
                    $this->procesarArchivo($ayuda_id, $usuario_id, $_FILES['evidencia'], 'Pendiente');
                }
                $this->redirect('/SGA-SEBANA/public/ayudas?success=creado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
                $this->logBitacora([
                    'accion' => 'CREATE',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'descripcion' => 'Error al crear solicitud de ayuda economica',
                    'resultado' => 'fallido',
                    'mensaje_error' => $this->model->getLastError()
                ]);
                $this->redirect('/SGA-SEBANA/public/ayudas/create?error=db_error');
            }
        }
    }

    public function requestCancellation($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioId = $this->getCurrentUserId();
            $ayuda = $this->model->obtenerPorId($id);

            if (!$usuarioId || !$ayuda || !$this->isOwner($ayuda, $usuarioId)) {
                $this->redirect('/SGA-SEBANA/public/ayudas?error=no_autorizado');
                return;
            }

            $motivo_cancelacion = trim($_POST['motivo_cancelacion'] ?? '');

            if ($this->model->registrarCancelacionUsuario($id, $motivo_cancelacion)) {
                $this->logBitacora([
                    'accion' => 'CANCEL',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'entidad_id' => (int) $id,
                    'descripcion' => 'Solicitud de cancelacion registrada por el afiliado',
                    'datos_nuevos' => ['motivo_cancelacion' => $motivo_cancelacion],
                    'resultado' => 'exitoso'
                ]);
                $this->notifyAdminsCancellationRequest((int) $id);
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?success=cancelacion_enviada');
            } else {
                $this->logBitacora([
                    'accion' => 'CANCEL',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'entidad_id' => (int) $id,
                    'descripcion' => 'Error al registrar solicitud de cancelacion',
                    'resultado' => 'fallido',
                    'mensaje_error' => $this->model->getLastError()
                ]);
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=cancel_error');
            }
        }
    }

    public function show($id) {
        $ayuda = $this->model->obtenerPorId($id);

        if (!$ayuda) {
            $this->redirect('/SGA-SEBANA/public/ayudas?error=not_found');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $esPropietario = $usuarioId ? $this->isOwner($ayuda, $usuarioId) : false;

        if (!$esJefatura && !$esPropietario) {
            $this->redirect('/SGA-SEBANA/public/ayudas?error=no_autorizado');
            return;
        }

        $evidencias = $this->model->obtenerEvidencias($id);

        $this->view('show', [
            'ayuda' => $ayuda,
            'evidencias' => $evidencias,
            'es_jefatura' => $esJefatura,
            'es_propietario' => $esPropietario
        ]);
    }

    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->isManager()) {
                $this->redirect('/SGA-SEBANA/public/ayudas?error=no_autorizado');
                return;
            }

            $nuevo_estado = $_POST['nuevo_estado'] ?? '';
            if ($nuevo_estado === '') {
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=estado');
                return;
            }

            if ($this->model->cambiarEstado($id, $nuevo_estado)) {
                $ayuda = $this->model->obtenerPorId($id);
                $this->logBitacora([
                    'accion' => 'STATUS_CHANGE',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'entidad_id' => (int) $id,
                    'descripcion' => "Cambio de estado de ayuda economica a {$nuevo_estado}",
                    'datos_nuevos' => ['estado' => $nuevo_estado],
                    'resultado' => 'exitoso'
                ]);
                if (is_array($ayuda)) {
                    $this->notifyAffiliateStatus((int) $id, (string) $nuevo_estado, $ayuda);
                }
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?success=estado_actualizado');
            } else {
                $this->logBitacora([
                    'accion' => 'STATUS_CHANGE',
                    'modulo' => 'ayudas',
                    'entidad' => 'solicitud_ayuda',
                    'entidad_id' => (int) $id,
                    'descripcion' => 'Error al actualizar estado de ayuda economica',
                    'resultado' => 'fallido',
                    'mensaje_error' => $this->model->getLastError()
                ]);
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=estado');
            }
        }
    }

    public function addEvidence($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
                return;
            }
            $usuario_id = (int) $_SESSION['user_id'];

            $ayuda = $this->model->obtenerPorId($id);
            if (!$ayuda || !$this->isOwner($ayuda, $usuario_id)) {
                $this->redirect('/SGA-SEBANA/public/ayudas?error=no_autorizado');
                return;
            }

            if (($ayuda['estado'] ?? '') === 'Pendiente') {
                if (isset($_FILES['nueva_evidencia']) && $_FILES['nueva_evidencia']['error'] === UPLOAD_ERR_OK) {
                    if (!$this->validarArchivo($_FILES['nueva_evidencia'])) {
                        $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=invalid_file');
                        return;
                    }

                    if ($this->procesarArchivo($id, $usuario_id, $_FILES['nueva_evidencia'], $ayuda['estado'])) {
                        $this->logBitacora([
                            'accion' => 'UPLOAD_FILE',
                            'modulo' => 'ayudas',
                            'entidad' => 'solicitud_ayuda',
                            'entidad_id' => (int) $id,
                            'descripcion' => 'Carga de evidencia en solicitud de ayuda economica',
                            'resultado' => 'exitoso'
                        ]);
                        $this->notifyAdminsEvidence((int) $id);
                        $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?success=evidencia_agregada');
                        return;
                    }
                }
            }
            $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=upload_failed');
        }
    }

    private function validarArchivo($file) {
        $fileSize = $file['size'];
        $fileNameCmps = explode('.', $file['name']);
        $fileExtension = strtolower(end($fileNameCmps));

        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
        $maxSize = 5 * 1024 * 1024;

        return in_array($fileExtension, $extensionesPermitidas, true) && $fileSize <= $maxSize;
    }

    private function procesarArchivo($ayuda_id, $usuario_id, $file, $estado_actual) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode('.', $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $nuevoNombreArchivo = md5(time() . $fileName) . '.' . $fileExtension;
        $directorioDestino = BASE_PATH . '/storage/ayudas/';

        if (!is_dir($directorioDestino)) {
            mkdir($directorioDestino, 0777, true);
        }

        $rutaDestinoFisica = $directorioDestino . $nuevoNombreArchivo;

        if (move_uploaded_file($fileTmpPath, $rutaDestinoFisica)) {
            $rutaArchivoFinal = 'storage/ayudas/' . $nuevoNombreArchivo;
            $this->model->guardarEvidencia($ayuda_id, $usuario_id, $fileName, $rutaArchivoFinal, $estado_actual);
            return true;
        }
        return false;
    }

    public function archivo($evidencia_id) {
        $evidencia = $this->model->obtenerEvidenciaPorId($evidencia_id);

        if (!$evidencia) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        if (!$usuarioId) {
            $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
            return;
        }

        if (!$this->isManager()) {
            $ayudaId = (int) ($evidencia['ayuda_id'] ?? 0);
            if ($ayudaId <= 0) {
                http_response_code(403);
                echo 'Acceso no permitido.';
                return;
            }

            $ayuda = $this->model->obtenerPorId($ayudaId);
            if (!$ayuda || !$this->isOwner($ayuda, $usuarioId)) {
                http_response_code(403);
                echo 'Acceso no permitido.';
                return;
            }
        }

        $rutaDb = $evidencia['path'] ?? null;
        $rutaStorage = $evidencia['ruta_archivo'] ?? null;
        $rutaBase = $rutaDb ?: $rutaStorage;

        if (empty($rutaBase)) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $rutaRelativa = ltrim($rutaBase, '/\\');
        $candidato = BASE_PATH . DIRECTORY_SEPARATOR . $rutaRelativa;
        $rutaFisica = file_exists($candidato) ? $candidato : null;

        if (!$rutaFisica && file_exists(BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $rutaRelativa)) {
            $rutaFisica = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $rutaRelativa;
        }

        if (!$rutaFisica || !file_exists($rutaFisica)) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $realBase = realpath(BASE_PATH);
        $realFile = realpath($rutaFisica);
        if (!$realBase || !$realFile || strpos($realFile, $realBase) !== 0) {
            http_response_code(403);
            echo 'Acceso no permitido.';
            return;
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($rutaFisica) : 'application/octet-stream';
        $disposition = isset($_GET['download']) ? 'attachment' : 'inline';

        header('Content-Type: ' . ($mime ?: 'application/octet-stream'));
        header('Content-Length: ' . filesize($rutaFisica));
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($rutaFisica) . '"');
        readfile($rutaFisica);
        exit;
    }
}
