<?php
namespace App\Modules\Viaticos\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Viaticos\Models\ViaticoModel;
use App\Modules\Usuarios\Helpers\AccessControl;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Modules\Visitas\Models\Notification; 

class ViaticoController extends ControllerBase {

    protected $model;
    protected $notiModel;


    public function __construct() {
        $this->model = new ViaticoModel();
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

    private function isOwner(array $viatico, int $usuarioId): bool {
        if (!empty($viatico['usuario_id']) && (int) $viatico['usuario_id'] === $usuarioId) {
            return true;
        }

        if (!empty($viatico['afiliado_id'])) {
            $miAfiliado = $this->model->obtenerAfiliadoIdPorUsuario($usuarioId);
            return $miAfiliado && ((int) $viatico['afiliado_id'] === (int) $miAfiliado);
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
            // No bloquear el flujo principal por falla de bitacora.
        }
    }

    public function index() {
        $usuarioId = $this->getCurrentUserId();
        if (!$usuarioId) {
            $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
            return;
        }

        $esJefatura = $this->isManager();
        $viaticos = $esJefatura
            ? $this->model->obtenerTodos()
            : $this->model->obtenerPorUsuario($usuarioId);

        $this->view('index', [
            'titulo' => 'Gestion de Viaticos',
            'viaticos' => $viaticos,
            'success' => $_GET['success'] ?? null,
            'es_jefatura' => $esJefatura
        ]);
    }

    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
            return;
        }

        $viatico = $this->model->obtenerPorId($id);

        if (!$viatico) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $esPropietario = $usuarioId ? $this->isOwner($viatico, $usuarioId) : false;

        if (!$esJefatura && !$esPropietario) {
            $this->redirect('/SGA-SEBANA/public/viaticos?error=no_autorizado');
            return;
        }

        $this->view('show', [
            'titulo' => 'Detalles del Viatico',
            'viatico' => $viatico,
            'es_jefatura' => $esJefatura,
            'es_propietario' => $esPropietario
        ]);
    }

    public function generarPDF() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
            return;
        }

        $viatico = $this->model->obtenerPorId($id);

        if (!$viatico) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $esPropietario = $usuarioId ? $this->isOwner($viatico, $usuarioId) : false;

        if (!$esJefatura && !$esPropietario) {
            $this->redirect('/SGA-SEBANA/public/viaticos?error=no_autorizado');
            return;
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        ob_start();
        require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/app/modules/viaticos/Views/pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('Boleta_Viaticos_' . $viatico['consecutivo'] . '.pdf', ['Attachment' => 0]);
    }

    public function create() {
        $esJefatura = $this->isManager();
        $this->view('create', [
            'titulo' => 'Nueva Solicitud de Viaticos',
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
            $usuario_id = (int) $_SESSION['user_id'];
            $afiliado_id = null;

            if ($this->isManager()) {
                $afiliado_id = !empty($_POST['afiliado_id']) ? (int) $_POST['afiliado_id'] : null;
                if (empty($afiliado_id)) {
                    $this->redirect('/SGA-SEBANA/public/viaticos/create?error=invalid_afiliado');
                    return;
                }
            }

            $rutaArchivoFinal = null;

            if (isset($_FILES['archivo_comprobante']) && $_FILES['archivo_comprobante']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['archivo_comprobante']['tmp_name'];
                $fileName = $_FILES['archivo_comprobante']['name'];
                $fileSize = $_FILES['archivo_comprobante']['size'];
                $fileNameCmps = explode('.', $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
                $maxSize = 5 * 1024 * 1024;

                if (in_array($fileExtension, $extensionesPermitidas, true) && $fileSize <= $maxSize) {
                    $nuevoNombreArchivo = md5(time() . $fileName) . '.' . $fileExtension;
                    $directorioDestino = BASE_PATH . '/storage/viaticos/';

                    if (!is_dir($directorioDestino)) {
                        mkdir($directorioDestino, 0777, true);
                    }

                    $rutaDestinoFisica = $directorioDestino . $nuevoNombreArchivo;

                    if (move_uploaded_file($fileTmpPath, $rutaDestinoFisica)) {
                        $rutaArchivoFinal = 'storage/viaticos/' . $nuevoNombreArchivo;
                    }
                }
            }

            $datos = [
                'empleados' => trim($_POST['empleados'] ?? ''),
                'fecha_inicio' => $_POST['fecha_inicio'] ?? null,
                'fecha_fin' => $_POST['fecha_fin'] ?? null,
                'cantidad_dias' => (int) ($_POST['cantidad_dias'] ?? 0),
                'cantidad_desayuno' => (int) ($_POST['cantidad_desayuno'] ?? 0),
                'cantidad_almuerzo' => (int) ($_POST['cantidad_almuerzo'] ?? 0),
                'cantidad_cena' => (int) ($_POST['cantidad_cena'] ?? 0),
                'cantidad_transportes' => (int) ($_POST['cantidad_transportes'] ?? 0),
                'aplica_transporte' => isset($_POST['aplica_transporte']) && $_POST['aplica_transporte'] == 1 ? 1 : 0,
                'tipo_vehiculo' => trim($_POST['v_type'] ?? ''),
                'kilometraje' => (float) ($_POST['v_km'] ?? 0),
                'tarifa_km' => (float) ($_POST['tarifa_km_oculta'] ?? 0),
                'monto_transporte' => (float) ($_POST['monto_transporte_oculto'] ?? 0),
                'enlace_maps' => trim($_POST['enlace_maps'] ?? ''),
                'aplica_desayuno' => isset($_POST['aplica_desayuno']) ? 1 : 0,
                'aplica_almuerzo' => isset($_POST['aplica_almuerzo']) ? 1 : 0,
                'aplica_cena' => isset($_POST['aplica_cena']) ? 1 : 0,
                'monto_alimentacion' => (float) ($_POST['monto_alimentacion_oculto'] ?? 0),
                'monto_hospedaje' => (float) ($_POST['monto_hospedaje'] ?? 0),
                'monto_gastos_menores' => (float) ($_POST['monto_gastos_menores'] ?? 0),
                'total_pagar' => (float) ($_POST['total_pagar_oculto'] ?? 0),
                'archivo_comprobante' => $rutaArchivoFinal,
                'afiliado_id' => $afiliado_id
            ];

            $datos['total_pagar'] = $datos['monto_alimentacion']
                + $datos['monto_transporte']
                + $datos['monto_hospedaje']
                + $datos['monto_gastos_menores'];

            $nuevoId = $this->model->crearSolicitud($datos, $usuario_id);

            if ($nuevoId) {
                $this->logBitacora([
                    'accion' => 'CREATE',
                    'modulo' => 'viaticos',
                    'entidad' => 'solicitud_viatico',
                    'entidad_id' => (int) $nuevoId,
                    'descripcion' => 'Creacion de solicitud de viaticos',
                    'datos_nuevos' => [
                        'afiliado_id' => $afiliado_id,
                        'total_pagar' => $datos['total_pagar'],
                        'fecha_inicio' => $datos['fecha_inicio'],
                        'fecha_fin' => $datos['fecha_fin']
                    ],
                    'resultado' => 'exitoso'
                ]);

                // Notificación
                $this->notiModel->createNotification(
                    $usuario_id,
                    'sistema',
                    'solicitudes',
                    'Nueva Solicitud de Viáticos',
                    "Se registró una nueva solicitud de viáticos con ID {$nuevoId}",
                    'solicitud_viatico',
                    $nuevoId,
                    "/SGA-SEBANA/public/viaticos/show?id={$nuevoId}"
                );




                unset($_SESSION['error_detail']);
                $this->redirect('/SGA-SEBANA/public/viaticos?success=creado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
                $this->logBitacora([
                    'accion' => 'CREATE',
                    'modulo' => 'viaticos',
                    'entidad' => 'solicitud_viatico',
                    'descripcion' => 'Error al crear solicitud de viaticos',
                    'resultado' => 'fallido',
                    'mensaje_error' => $this->model->getLastError()
                ]);
                $this->redirect('/SGA-SEBANA/public/viaticos/create?error=db');
            }
        }
    }

    public function archivo() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
            return;
        }

        $viatico = $this->model->obtenerPorId($id);

        if (!$viatico || empty($viatico['archivo_comprobante'])) {
            $this->redirect('/SGA-SEBANA/public/viaticos/show?id=' . $id);
            return;
        }

        $usuarioId = $this->getCurrentUserId();
        $esJefatura = $this->isManager();
        $esPropietario = $usuarioId ? $this->isOwner($viatico, $usuarioId) : false;

        if (!$esJefatura && !$esPropietario) {
            $this->redirect('/SGA-SEBANA/public/viaticos?error=no_autorizado');
            return;
        }

        $rutaRelativa = ltrim($viatico['archivo_comprobante'], '/\\');
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

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->isManager()) {
                $this->redirect('/SGA-SEBANA/public/viaticos?error=no_autorizado');
                return;
            }

            $id = $_POST['id'] ?? null;
            $nuevo_estado = trim($_POST['nuevo_estado'] ?? '');
            if (!$id || $nuevo_estado === '') {
                $this->redirect('/SGA-SEBANA/public/viaticos');
                return;
            }

            if ($this->model->cambiarEstado($id, $nuevo_estado)) {
                $this->logBitacora([
                    'accion' => 'STATUS_CHANGE',
                    'modulo' => 'viaticos',
                    'entidad' => 'solicitud_viatico',
                    'entidad_id' => (int) $id,
                    'descripcion' => "Cambio de estado de viaticos a {$nuevo_estado}",
                    'datos_nuevos' => ['estado' => $nuevo_estado],
                    'resultado' => 'exitoso'
                ]);


                // Notificación
                $this->notiModel->createNotification(
                    1,
                    'sistema',
                    'solicitudes',
                    'Estado de Viáticos Actualizado',
                    "La solicitud de viáticos ID {$id} ahora está en estado: {$nuevo_estado}",
                    'solicitud_viatico',
                    $id,
                    "/SGA-SEBANA/public/viaticos/show?id={$id}"
                );

                $this->redirect('/SGA-SEBANA/public/viaticos/show?id=' . $id . '&success=estado_actualizado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
                $this->logBitacora([
                    'accion' => 'STATUS_CHANGE',
                    'modulo' => 'viaticos',
                    'entidad' => 'solicitud_viatico',
                    'entidad_id' => (int) $id,
                    'descripcion' => 'Error al actualizar estado de viaticos',
                    'resultado' => 'fallido',
                    'mensaje_error' => $this->model->getLastError()
                ]);
                $this->redirect('/SGA-SEBANA/public/viaticos/show?id=' . $id . '&error=estado');
            }
        }
    }
}
