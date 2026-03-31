<?php
namespace App\Modules\Ayudas\Controllers;

use App\Core\ControllerBase;
use App\Modules\Ayudas\Models\AyudaEconomicaModel;

class AyudaEconomicaController extends ControllerBase {
    
    protected $model;

    public function __construct() {
        $this->model = new AyudaEconomicaModel();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        $ayudas = $this->model->obtenerTodas();
        
        $this->view('index', [
            'ayudas' => $ayudas
        ]);
    }

    public function create() {
        $this->view('create', [
            'titulo' => 'Solicitar Ayuda Económica',
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CORTE 1 CORREGIDO: Usamos 'user_id' que es la llave real de tu sistema
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
                return;
            }
            $usuario_id = $_SESSION['user_id']; 
            
            // CORTE 2: HU-SAEC-3 E2 - Validar archivo ANTES de crear el registro en BD
            if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
                if (!$this->validarArchivo($_FILES['evidencia'])) {
                    $this->redirect('/SGA-SEBANA/public/ayudas/create?error=invalid_file');
                    return;
                }
            }

            $motivo = trim($_POST['motivo'] ?? '');
            $monto_solicitado = floatval($_POST['monto_solicitado'] ?? 0);

            // NUEVO: Validación estricta del monto máximo (100,000 colones)
            if ($monto_solicitado > 100000) {
                $this->redirect('/SGA-SEBANA/public/ayudas/create?error=monto_excedido');
                return;
            }

            $ayuda_id = $this->model->crearSolicitud($usuario_id, $motivo, $monto_solicitado);

            if ($ayuda_id) {
                unset($_SESSION['error_detail']);
                // HU-SAEC-3: Adjuntar evidencia inicial
                if (isset($_FILES['evidencia']) && $_FILES['evidencia']['error'] === UPLOAD_ERR_OK) {
                    $this->procesarArchivo($ayuda_id, $usuario_id, $_FILES['evidencia'], 'Pendiente');
                }
                $this->redirect('/SGA-SEBANA/public/ayudas?success=creado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
                $this->redirect('/SGA-SEBANA/public/ayudas/create?error=db_error');
            }
        }
    }

    public function requestCancellation($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $motivo_cancelacion = trim($_POST['motivo_cancelacion'] ?? '');
            
            if ($this->model->registrarCancelacionUsuario($id, $motivo_cancelacion)) {
                $this->enviarNotificacionAdmin($id, "Un usuario ha solicitado la cancelación de su ayuda económica.");
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?success=cancelacion_enviada');
            } else {
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

        $evidencias = $this->model->obtenerEvidencias($id);

        $this->view('show', [
            'ayuda' => $ayuda,
            'evidencias' => $evidencias
        ]);
    }

    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nuevo_estado = $_POST['nuevo_estado'];
            
            if ($this->model->cambiarEstado($id, $nuevo_estado)) {
                $ayuda = $this->model->obtenerPorId($id);
                $this->notificarCambioEstado($ayuda['correo'], $nuevo_estado, $id);
                $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?success=estado_actualizado');
            }
        }
    }

    public function addEvidence($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CORTE 3 CORREGIDO: Sesión estricta usando 'user_id'
            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
                return;
            }
            $usuario_id = $_SESSION['user_id'];
            
            $ayuda = $this->model->obtenerPorId($id);

            if ($ayuda && $ayuda['estado'] === 'Pendiente') {
                if (isset($_FILES['nueva_evidencia']) && $_FILES['nueva_evidencia']['error'] === UPLOAD_ERR_OK) {
                    
                    if (!$this->validarArchivo($_FILES['nueva_evidencia'])) {
                        $this->redirect('/SGA-SEBANA/public/ayudas/show/' . $id . '?error=invalid_file');
                        return;
                    }

                    if ($this->procesarArchivo($id, $usuario_id, $_FILES['nueva_evidencia'], $ayuda['estado'])) {
                        $this->enviarNotificacionAdmin($id, "Se ha adjuntado nueva evidencia a la solicitud.");
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
        $fileNameCmps = explode(".", $file['name']);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        return in_array($fileExtension, $extensionesPermitidas) && $fileSize <= $maxSize;
    }

    private function procesarArchivo($ayuda_id, $usuario_id, $file, $estado_actual) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
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

        $rutaDb = $evidencia['path'] ?? null;
        $rutaStorage = $evidencia['ruta_archivo'] ?? null;
        $rutaBase = $rutaDb ?: $rutaStorage;

        if (!$evidencia || empty($rutaBase)) {
            http_response_code(404);
            echo "Archivo no encontrado.";
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
            echo "Archivo no encontrado.";
            return;
        }

        $realBase = realpath(BASE_PATH);
        $realFile = realpath($rutaFisica);
        if (!$realBase || !$realFile || strpos($realFile, $realBase) !== 0) {
            http_response_code(403);
            echo "Acceso no permitido.";
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

    private function notificarCambioEstado($correo, $estado, $id) {
        $asunto = "Actualización de Solicitud SEBANA #$id";
        $mensaje = "Hola, le informamos que su solicitud de ayuda económica ha sido actualizada al estado: $estado.";
    }

    private function enviarNotificacionAdmin($id, $accion) {
        $admins = $this->model->obtenerCorreosAdministradores();
        foreach ($admins as $email) {
            // Notificar al admin sobre nueva evidencia o cancelación
        }
    }
}