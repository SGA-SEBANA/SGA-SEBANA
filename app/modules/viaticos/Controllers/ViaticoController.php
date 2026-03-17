<?php
namespace App\Modules\Viaticos\Controllers;

use App\Core\ControllerBase;
use App\Modules\Viaticos\Models\ViaticoModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class ViaticoController extends ControllerBase {
    
    protected $model;

    public function __construct() {
        $this->model = new ViaticoModel();
    }

    /**
     * Muestra el listado principal de viáticos
     */
    public function index() {
        $viaticos = $this->model->obtenerTodos();
        
        $this->view('index', [
            'titulo' => 'Gestión de Viáticos',
            'viaticos' => $viaticos,
            'success' => $_GET['success'] ?? null
        ]);
    }

    /**
     * Muestra los detalles completos de una solicitud
     */
    public function show() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
        }

        $viatico = $this->model->obtenerPorId($id);

        if (!$viatico) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
        }

        $this->view('show', [
            'titulo' => 'Detalles del Viático',
            'viatico' => $viatico
        ]);
    }

    /**
     * Genera la Boleta Oficial en PDF (HU-GCV-07)
     */
    public function generarPDF() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
        }

        $viatico = $this->model->obtenerPorId($id);

        if (!$viatico) {
            $this->redirect('/SGA-SEBANA/public/viaticos');
        }

        // 1. Configuramos Dompdf
        $options = new Options();
        $options->set('isRemoteEnabled', true); // Permite cargar CSS o imágenes
        $dompdf = new Dompdf($options);

        // 2. Capturamos el HTML de nuestra vista especial para el PDF
        ob_start();
        // Incluimos directamente la vista del PDF (sin el layout general del sistema)
        require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/app/modules/viaticos/Views/pdf.php';
        $html = ob_get_clean();

        // 3. Cargamos el HTML en el motor de PDF
        $dompdf->loadHtml($html);
        
        // 4. Formato de hoja (A4, vertical)
        $dompdf->setPaper('A4', 'portrait');
        
        // 5. Renderizamos
        $dompdf->render();

        // 6. Mostramos el PDF en el navegador (Attachment 0 = Ver, Attachment 1 = Descargar directo)
        $dompdf->stream("Boleta_Viaticos_" . $viatico['consecutivo'] . ".pdf", array("Attachment" => 0));
    }

    /**
     * Muestra el formulario para crear una nueva solicitud
     */
    public function create() {
        $this->view('create', [
            'titulo' => 'Nueva Solicitud de Viáticos',
            'error' => $_GET['error'] ?? null
        ]);
    }

    /**
     * Procesa y guarda la nueva solicitud en la Base de Datos
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if (!isset($_SESSION['user_id'])) {
                $this->redirect('/SGA-SEBANA/public/login?error=sesion_expirada');
                return;
            }
            $usuario_id = $_SESSION['user_id'];
            
            $rutaArchivoFinal = null;

            if (isset($_FILES['archivo_comprobante']) && $_FILES['archivo_comprobante']['error'] === UPLOAD_ERR_OK) {
                
                $fileTmpPath = $_FILES['archivo_comprobante']['tmp_name'];
                $fileName = $_FILES['archivo_comprobante']['name'];
                $fileSize = $_FILES['archivo_comprobante']['size'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
                $maxSize = 5 * 1024 * 1024; // 5MB

                if (in_array($fileExtension, $extensionesPermitidas) && $fileSize <= $maxSize) {
                    
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
                'cantidad_dias' => intval($_POST['cantidad_dias'] ?? 0),
                'cantidad_desayuno' => intval($_POST['cantidad_desayuno'] ?? 0),
                'cantidad_almuerzo' => intval($_POST['cantidad_almuerzo'] ?? 0),
                'cantidad_cena' => intval($_POST['cantidad_cena'] ?? 0),
                'cantidad_transportes' => intval($_POST['cantidad_transportes'] ?? 0),
                'aplica_transporte' => isset($_POST['aplica_transporte']) && $_POST['aplica_transporte'] == 1 ? 1 : 0,
                'tipo_vehiculo' => trim($_POST['v_type'] ?? ''), 
                'kilometraje' => floatval($_POST['v_km'] ?? 0),
                'tarifa_km' => floatval($_POST['tarifa_km_oculta'] ?? 0), 
                'monto_transporte' => floatval($_POST['monto_transporte_oculto'] ?? 0), 
                'enlace_maps' => trim($_POST['enlace_maps'] ?? ''),
                'aplica_desayuno' => isset($_POST['aplica_desayuno']) ? 1 : 0,
                'aplica_almuerzo' => isset($_POST['aplica_almuerzo']) ? 1 : 0,
                'aplica_cena' => isset($_POST['aplica_cena']) ? 1 : 0,
                'monto_alimentacion' => floatval($_POST['monto_alimentacion_oculto'] ?? 0), 
                'monto_hospedaje' => floatval($_POST['monto_hospedaje'] ?? 0),
                'monto_gastos_menores' => floatval($_POST['monto_gastos_menores'] ?? 0),
                'total_pagar' => floatval($_POST['total_pagar_oculto'] ?? 0),
                'archivo_comprobante' => $rutaArchivoFinal 
            ];

            $datos['total_pagar'] = $datos['monto_alimentacion']
                + $datos['monto_transporte']
                + $datos['monto_hospedaje']
                + $datos['monto_gastos_menores'];

            $nuevoId = $this->model->crearSolicitud($datos, $usuario_id);

            if ($nuevoId) {
                unset($_SESSION['error_detail']);
                $this->redirect('/SGA-SEBANA/public/viaticos?success=creado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
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

        $rutaRelativa = ltrim($viatico['archivo_comprobante'], '/\\');
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

    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $nuevo_estado = trim($_POST['nuevo_estado'] ?? '');
            if (!$id || $nuevo_estado === '') {
                $this->redirect('/SGA-SEBANA/public/viaticos');
                return;
            }

            if ($this->model->cambiarEstado($id, $nuevo_estado)) {
                $this->redirect('/SGA-SEBANA/public/viaticos/show?id=' . $id . '&success=estado_actualizado');
            } else {
                $_SESSION['error_detail'] = $this->model->getLastError();
                $this->redirect('/SGA-SEBANA/public/viaticos/show?id=' . $id . '&error=estado');
            }
        }
    }
}
