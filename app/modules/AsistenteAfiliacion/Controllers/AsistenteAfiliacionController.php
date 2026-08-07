<?php
namespace App\Modules\AsistenteAfiliacion\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\AsistenteAfiliacion\Models\AsistenteAfiliacionModel;
use App\Modules\Usuarios\Helpers\AccessControl;
use App\Modules\Usuarios\Helpers\AffiliateAccountProvisioner;
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Usuarios\Models\User;
use App\Modules\Visitas\Models\Notification;
use Dompdf\Dompdf;
use Dompdf\Options;

class AsistenteAfiliacionController extends ControllerBase
{
    private $model;

    public function __construct()
    {
        $this->model = new AsistenteAfiliacionModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
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

    private function isManager()
    {
        if (!SecurityHelper::isAuthenticated()) {
            return false;
        }

        return AccessControl::hasLevel('alto');
    }

    private function requireManager()
    {
        if (!SecurityHelper::isAuthenticated()) {
            $this->redirect('/SGA-SEBANA/public/login');
            return false;
        }

        if (!$this->isManager()) {
            $this->redirect('/SGA-SEBANA/public/home?error=no_autorizado');
            return false;
        }

        return true;
    }

    private function collectFormData()
    {
        $oficinaId = (int) ($_POST['oficina_id'] ?? 0);
        $oficinaNombre = trim((string) ($_POST['oficina_bncr'] ?? ''));
        if ($oficinaId > 0) {
            $nombreDb = $this->model->getNombreOficinaById($oficinaId);
            if (!empty($nombreDb)) {
                $oficinaNombre = trim((string) $nombreDb);
            }
        }

        return [
            'tipo_usuario' => strtolower(trim((string) ($_POST['tipo_usuario'] ?? ''))),
            'cedula' => trim((string) ($_POST['cedula'] ?? '')),
            'nombre' => trim((string) ($_POST['nombre'] ?? '')),
            'apellido1' => trim((string) ($_POST['apellido1'] ?? '')),
            'apellido2' => trim((string) ($_POST['apellido2'] ?? '')),
            'correo' => trim((string) ($_POST['correo'] ?? '')),
            'fecha_nacimiento' => trim((string) ($_POST['fecha_nacimiento'] ?? '')),
            'numero_empleado' => trim((string) ($_POST['numero_empleado'] ?? '')),
            'oficina_bncr' => $oficinaNombre,
            'oficina_id' => $oficinaId > 0 ? $oficinaId : null,
            'categoria_id' => !empty($_POST['categoria_id']) ? (int) $_POST['categoria_id'] : null,
            'departamento' => trim((string) ($_POST['departamento'] ?? '')),
            'puesto' => trim((string) ($_POST['puesto'] ?? '')),
            'fecha_ingreso_bncr' => trim((string) ($_POST['fecha_ingreso_bncr'] ?? '')),
            'fecha_jubilacion' => trim((string) ($_POST['fecha_jubilacion'] ?? '')),
            'celular' => $this->model->normalizePhone($_POST['celular'] ?? ''),
            'acepta_deduccion' => isset($_POST['acepta_deduccion']) ? 1 : 0,
            'acepta_estatuto' => isset($_POST['acepta_estatuto']) ? 1 : 0,
            'observaciones' => trim((string) ($_POST['observaciones'] ?? ''))
        ];
    }

    private function getFlash($key)
    {
        $value = $_SESSION[$key] ?? null;
        unset($_SESSION[$key]);
        return $value;
    }

private function validateData(array $data, $requireSignedFile = false, $excludeDuplicateId = null)
{
    $errors = [];

    if (!$this->model->isTipoUsuarioPermitido($data['tipo_usuario'])) {
        $errors[] = 'Solo se permite afiliacion para personal BNCR activo o jubilado.';
    }

    if (!preg_match('/^[0-9]{9}$/', $data['cedula'])) {
        $errors[] = 'La cedula debe tener exactamente 9 digitos numericos.';
    }

    if (strlen($data['nombre']) > 50) {
        $errors[] = 'El nombre no puede superar los 50 caracteres.';
    }

 
    if (strlen($data['apellido1']) > 50) {
        $errors[] = 'El primer apellido no puede superar los 50 caracteres.';
    }


    if (strlen($data['apellido2']) > 50) {
        $errors[] = 'El segundo apellido no puede superar los 50 caracteres.';
    }

    if (strlen($data['correo']) > 100) {
        $errors[] = 'El correo no puede superar los 100 caracteres.';
    }


    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Debe ingresar un correo electronico valido.';
    }

    if ($data['fecha_nacimiento'] === '') {
        $errors[] = 'La fecha de nacimiento es obligatoria.';
    }

    if ($data['numero_empleado'] === '' || $data['oficina_bncr'] === '' || $data['departamento'] === '' || $data['puesto'] === '') {
        $errors[] = 'Debe completar la informacion laboral del BNCR.';
    }

  
    if (strlen($data['numero_empleado']) > 20) {
        $errors[] = 'El numero de empleado no puede superar los 20 caracteres.';
    }


    if (!preg_match('/^[0-9]{1,20}$/', $data['numero_empleado'])) {
        $errors[] = 'El numero de empleado debe ser numerico.';
    }


    if (strlen($data['departamento']) > 100) {
        $errors[] = 'El departamento no puede superar los 100 caracteres.';
    }


    if (strlen($data['puesto']) > 100) {
        $errors[] = 'El puesto no puede superar los 100 caracteres.';
    }

 
    if (strlen($data['observaciones']) > 500) {
        $errors[] = 'Las observaciones no pueden superar los 500 caracteres.';
    }

    if (empty($data['oficina_id'])) {
        $errors[] = 'Debe seleccionar una oficina BNCR valida.';
    }

    if ($data['fecha_ingreso_bncr'] === '') {
        $errors[] = 'La fecha de ingreso al BNCR es obligatoria.';
    }

    if ($data['tipo_usuario'] === 'jubilado' && $data['fecha_jubilacion'] === '') {
        $errors[] = 'Si es jubilado debe indicar fecha de jubilacion.';
    }

    if (!preg_match('/^\+?[0-9]{8}$/', $data['celular'])) {
        $errors[] = 'Debe ingresar un numero de celular valido.';
    }

    if ((int) $data['acepta_deduccion'] !== 1) {
        $errors[] = 'Debe aceptar la deduccion salarial del 1%.';
    }

    if ((int) $data['acepta_estatuto'] !== 1) {
        $errors[] = 'Debe aceptar el estatuto para continuar.';
    }

    if ($data['cedula'] !== '' && $this->model->cedulaDuplicada($data['cedula'], $excludeDuplicateId)) {
        $errors[] = 'Ya existe una afiliacion registrada o en tramite con esta cedula.';
    }

    if ($data['correo'] !== '' && $this->model->correoDuplicado($data['correo'], $excludeDuplicateId)) {
        $errors[] = 'Ya existe una afiliacion registrada o en tramite con este correo.';
    }

    if ($requireSignedFile) {
        [$okPdf, $errorPdf] = $this->validateSignedUpload($_FILES['pdf_firmado'] ?? null);
        if (!$okPdf) {
            $errors[] = $errorPdf;
        }
    }

    return $errors;
}

    private function validateSignedUpload($file)
    {
        if (!$file || !isset($file['error'])) {
            return [false, 'Debe adjuntar el PDF firmado.'];
        }

        if ((int) $file['error'] !== UPLOAD_ERR_OK) {
            return [false, 'No se pudo procesar el archivo firmado.'];
        }

        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            return [false, 'Solo se permiten archivos PDF firmados.'];
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > (10 * 1024 * 1024)) {
            return [false, 'El PDF firmado debe ser mayor a 0 y menor a 10MB.'];
        }

        $tmp = $file['tmp_name'] ?? '';
        if ($tmp === '' || !file_exists($tmp)) {
            return [false, 'No se encontro el archivo temporal subido.'];
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($tmp) : '';
        if ($mime !== '' && $mime !== 'application/pdf' && $mime !== 'application/octet-stream') {
            return [false, 'El archivo adjunto no es un PDF valido.'];
        }

        $fh = fopen($tmp, 'rb');
        if (!$fh) {
            return [false, 'No se pudo validar el contenido del PDF.'];
        }
        $signature = fread($fh, 4);
        fclose($fh);

        if ($signature !== '%PDF') {
            return [false, 'El archivo no tiene formato PDF valido.'];
        }

        return [true, null];
    }

    private function renderPdfBinary(array $solicitud)
    {
        ob_start();
        require BASE_PATH . '/app/modules/AsistenteAfiliacion/Views/pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function streamPdf($filename, $binary, $attachment)
    {
        header('Content-Type: application/pdf');
        header('Content-Length: ' . strlen($binary));
        header('Content-Disposition: ' . ($attachment ? 'attachment' : 'inline') . '; filename="' . $filename . '"');
        echo $binary;
        exit;
    }

    private function notifySebana(array $solicitud)
    {
        $notification = new Notification();
        $userModel = new User();
        $admins = $userModel->getAdmins();

        $nombre = trim(($solicitud['nombre'] ?? '') . ' ' . ($solicitud['apellido1'] ?? ''));
        $cedula = $solicitud['cedula'] ?? 'N/D';
        $id = (int) ($solicitud['id'] ?? 0);

        foreach ($admins as $admin) {
            $adminId = (int) ($admin['id'] ?? 0);
            if ($adminId <= 0) {
                continue;
            }

            $notification->createNotification(
                $adminId,
                'sistema',
                'afiliados',
                'Nueva solicitud de afiliacion',
                "Se recibio solicitud de afiliacion de {$nombre} (cedula {$cedula}).",
                'solicitud_afiliacion',
                $id,
                "/SGA-SEBANA/public/asistente-afiliacion/solicitudes/{$id}",
                'normal'
            );
        }
    }

    private function resolveDraftIdFromPost()
    {
        $draftId = $_POST['draft_id'] ?? ($_SESSION['afiliacion_draft_id'] ?? null);
        return ($draftId !== null && $draftId !== '') ? (string) $draftId : null;
    }

    private function normalizeLookup($value)
    {
        $value = strtolower(trim((string) $value));
        return preg_replace('/\s+/', ' ', $value) ?? '';
    }

    private function logBitacora(array $data): void
    {
        try {
            $bitacora = new Bitacora();
            $bitacora->log($data);
        } catch (\Throwable $e) {
            // Bitacora no debe detener procesos de afiliacion.
        }
    }

    private function resolveOficinaId(string $oficinaTexto, Afiliados $afiliadosModel): ?int
    {
        $needle = $this->normalizeLookup($oficinaTexto);
        if ($needle === '') {
            return null;
        }

        foreach ($afiliadosModel->getOficinas() as $oficina) {
            $codigo = $this->normalizeLookup((string) ($oficina['codigo'] ?? ''));
            $nombre = $this->normalizeLookup((string) ($oficina['nombre'] ?? ''));

            if ($needle === $codigo || $needle === $nombre) {
                return (int) ($oficina['id'] ?? 0);
            }

            if (($codigo !== '' && str_contains($needle, $codigo)) || ($nombre !== '' && str_contains($needle, $nombre))) {
                return (int) ($oficina['id'] ?? 0);
            }
        }

        return null;
    }

    private function resolveCategoriaId($categoriaId, Afiliados $afiliadosModel): ?int
    {
        $categoriaId = (int) $categoriaId;
        $categorias = $afiliadosModel->getCategorias();
        if (empty($categorias)) {
            return null;
        }

        if ($categoriaId > 0) {
            foreach ($categorias as $categoria) {
                if ((int) ($categoria['id'] ?? 0) === $categoriaId) {
                    return $categoriaId;
                }
            }
        }

        return (int) ($categorias[0]['id'] ?? 0) ?: null;
    }

    private function ensureAffiliateAndUserForApproval(array $solicitud): array
    {
        $afiliadosModel = new Afiliados();
        $cedula = trim((string) ($solicitud['cedula'] ?? ''));

        if ($cedula === '') {
            return [
                'success' => false,
                'error' => 'La solicitud no tiene cedula para crear afiliado/usuario.'
            ];
        }

        $afiliado = $afiliadosModel->getByCedula($cedula);
        $afiliadoId = (int) ($afiliado['id'] ?? 0);
        $afiliadoCreado = false;

        if ($afiliadoId <= 0) {
            $oficinaId = !empty($solicitud['oficina_id']) ? (int) $solicitud['oficina_id'] : null;
            if (!empty($oficinaId)) {
                $oficinaValida = false;
                foreach ($afiliadosModel->getOficinas() as $oficina) {
                    if ((int) ($oficina['id'] ?? 0) === (int) $oficinaId) {
                        $oficinaValida = true;
                        break;
                    }
                }
                if (!$oficinaValida) {
                    $oficinaId = null;
                }
            }

            if (empty($oficinaId)) {
                $oficinaId = $this->resolveOficinaId((string) ($solicitud['oficina_bncr'] ?? ''), $afiliadosModel);
            }

            $categoriaId = $this->resolveCategoriaId($solicitud['categoria_id'] ?? null, $afiliadosModel);
            $observaciones = trim((string) ($solicitud['observaciones'] ?? ''));
            $observaciones = $observaciones !== ''
                ? ('Solicitud asistente: ' . $observaciones)
                : 'Afiliado creado automaticamente desde solicitud aprobada.';

            $correoSolicitud = trim((string) ($solicitud['correo'] ?? ''));
            if ($correoSolicitud !== '') {
                $afiliadoPorCorreo = $afiliadosModel->getByCorreo($correoSolicitud);
                if ($afiliadoPorCorreo && (int) ($afiliadoPorCorreo['id'] ?? 0) > 0) {
                    return [
                        'success' => false,
                        'error' => 'El correo de la solicitud ya pertenece a otro afiliado. Actualice el correo antes de aprobar.'
                    ];
                }
            }

            $nuevoId = (int) $afiliadosModel->create([
                'nombre' => trim((string) ($solicitud['nombre'] ?? '')),
                'apellido1' => trim((string) ($solicitud['apellido1'] ?? '')),
                'apellido2' => trim((string) ($solicitud['apellido2'] ?? '')),
                'cedula' => $cedula,
                'genero' => 'prefiero_no_decir',
                'fecha_nacimiento' => trim((string) ($solicitud['fecha_nacimiento'] ?? '')),
                'correo' => trim((string) ($solicitud['correo'] ?? '')),
                'telefono' => trim((string) ($solicitud['celular'] ?? '')),
                'telefono_secundario' => null,
                'direccion' => null,
                'categoria_id' => $categoriaId,
                'oficina_id' => $oficinaId,
                'puesto_actual' => trim((string) ($solicitud['puesto'] ?? '')),
                'datos_contacto_emergencia' => '{}',
                'observaciones' => $observaciones
            ]);

            if ($nuevoId <= 0) {
                return [
                    'success' => false,
                    'error' => 'No se pudo crear el afiliado en base de datos durante la aprobacion.'
                ];
            }

            $afiliadoId = $nuevoId;
            $afiliadoCreado = true;
        }

        $provisioner = new AffiliateAccountProvisioner();
        $userProvision = $provisioner->provision([
            'cedula' => $cedula,
            'correo' => trim((string) ($solicitud['correo'] ?? '')),
            'nombre' => trim((string) ($solicitud['nombre'] ?? '')),
            'apellido1' => trim((string) ($solicitud['apellido1'] ?? '')),
            'apellido2' => trim((string) ($solicitud['apellido2'] ?? '')),
            'telefono' => trim((string) ($solicitud['celular'] ?? ''))
        ]);

        if (!($userProvision['success'] ?? false)) {
            return [
                'success' => false,
                'error' => 'Se creo/valido el afiliado, pero no se pudo crear el usuario: ' . ($userProvision['error'] ?? 'Error no especificado.')
            ];
        }

        return [
            'success' => true,
            'afiliado_id' => $afiliadoId,
            'afiliado_created' => $afiliadoCreado,
            'user_provision' => $userProvision
        ];
    }

    private function buildApprovalProvisionMessage(array $sync): string
    {
        $parts = [];
        $parts[] = ($sync['afiliado_created'] ?? false)
            ? 'Afiliado creado automaticamente.'
            : 'Afiliado ya existente en base de datos.';

        $userProvision = $sync['user_provision'] ?? [];
        if (($userProvision['created'] ?? false) === true) {
            $parts[] = 'Usuario creado: ' . ($userProvision['username'] ?? 'N/D');
            $parts[] = 'Contrasena temporal: ' . ($userProvision['temp_password'] ?? 'N/D');
        } else {
            $parts[] = 'El usuario ya existia, no se creo uno nuevo.';
        }

        return implode(' ', $parts);
    }

    public function create()
    {
        $draftId = $_GET['draft'] ?? ($_SESSION['afiliacion_draft_id'] ?? null);
        $draft = null;

        if (!empty($draftId)) {
            $draft = $this->model->read($draftId);
        }

        $formData = $draft ?: ($_SESSION['afiliacion_form_data'] ?? []);
        $errors = $this->getFlash('afiliacion_errors') ?? [];
        $oficinas = $this->model->getOficinasDisponibles();
        $categorias = $this->model->getCategoriasAfiliacion();

        $this->view('create', [
            'title' => 'Asistente de Afiliacion a SEBANA',
            'form' => $formData,
            'draft_id' => $draft['id'] ?? ($draftId ?: ''),
            'errors' => $errors,
            'success' => $_GET['success'] ?? null,
            'status' => $draft['estado'] ?? null,
            'oficinas' => $oficinas,
            'categorias' => $categorias
        ]);
    }

    public function generatePdf()
    {
        $this->handleGeneratePdf(false);
    }

    public function downloadPdf()
    {
        $this->handleGeneratePdf(true);
    }

    private function handleGeneratePdf($download)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/afiliarse');
            return;
        }

        $data = $this->collectFormData();
        $_SESSION['afiliacion_form_data'] = $data;

        $existingDraft = $this->model->findDraftByCedula($data['cedula']);
        $draftId = $this->resolveDraftIdFromPost();
        $excludeId = $draftId ?: ($existingDraft['id'] ?? null);

        $errors = $this->validateData($data, false, $excludeId);
        if (!empty($errors)) {
            $_SESSION['afiliacion_errors'] = $errors;
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=validacion');
            return;
        }

        $draft = $this->model->saveDraft($data, $excludeId);
        if (!$draft) {
            $_SESSION['afiliacion_errors'] = ['No se pudo guardar el borrador: ' . $this->model->getLastError()];
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=guardado');
            return;
        }

        $binary = $this->renderPdfBinary($draft);
        if (!$binary) {
            $_SESSION['afiliacion_errors'] = ['No se pudo generar el PDF.'];
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=pdf');
            return;
        }

        if (!$this->model->saveGeneratedPdf($draft['id'], $binary)) {
            $_SESSION['afiliacion_errors'] = ['No se pudo almacenar el PDF generado: ' . $this->model->getLastError()];
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=pdf_storage');
            return;
        }

        $_SESSION['afiliacion_draft_id'] = $draft['id'];

        $filename = 'Formulario_Afiliacion_' . $this->model->normalizeCedula($data['cedula']) . '.pdf';
        $this->streamPdf($filename, $binary, $download);
    }

    public function submit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/afiliarse');
            return;
        }

        $data = $this->collectFormData();
        $_SESSION['afiliacion_form_data'] = $data;

        $existingDraft = $this->model->findDraftByCedula($data['cedula']);
        $draftId = $this->resolveDraftIdFromPost();
        $excludeId = $draftId ?: ($existingDraft['id'] ?? null);

        $errors = $this->validateData($data, true, $excludeId);
        if (!empty($errors)) {
            $_SESSION['afiliacion_errors'] = $errors;
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=validacion');
            return;
        }

        $draft = $this->model->saveDraft($data, $excludeId);
        if (!$draft) {
            $_SESSION['afiliacion_errors'] = ['No se pudo guardar la solicitud: ' . $this->model->getLastError()];
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=guardado');
            return;
        }

        $draftId = $draft['id'];

        $absoluteUnsigned = $this->model->buildAbsolutePath($draft['pdf_generado_path'] ?? null);
        if (!$absoluteUnsigned) {
            $binary = $this->renderPdfBinary($draft);
            if (!$binary || !$this->model->saveGeneratedPdf($draftId, $binary)) {
                $_SESSION['afiliacion_errors'] = ['Debe generar primero el PDF de afiliacion antes de enviar.'];
                $this->redirect('/SGA-SEBANA/public/afiliarse?error=pdf_requerido');
                return;
            }
        }

        $signedPath = $this->model->saveSignedPdf(
            $draftId,
            $_FILES['pdf_firmado']['tmp_name'],
            $_FILES['pdf_firmado']['name']
        );

        if (!$signedPath) {
            $_SESSION['afiliacion_errors'] = ['No se pudo guardar el PDF firmado: ' . $this->model->getLastError()];
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=firma');
            return;
        }

        if (!$this->model->submitForApproval($draftId, $data, $signedPath)) {
            $_SESSION['afiliacion_errors'] = ['No se pudo enviar la solicitud: ' . $this->model->getLastError()];
            $this->logBitacora([
                'accion' => 'SUBMIT',
                'modulo' => 'asistente_afiliacion',
                'entidad' => 'solicitud_afiliacion',
                'entidad_id' => (int) $draftId,
                'descripcion' => 'Error al enviar solicitud de afiliacion',
                'resultado' => 'fallido',
                'mensaje_error' => $this->model->getLastError()
            ]);
            $this->redirect('/SGA-SEBANA/public/afiliarse?error=envio');
            return;
        }

        $submitted = $this->model->read($draftId);
        if ($submitted) {
            $this->logBitacora([
                'accion' => 'SUBMIT',
                'modulo' => 'asistente_afiliacion',
                'entidad' => 'solicitud_afiliacion',
                'entidad_id' => (int) $draftId,
                'descripcion' => 'Envio de solicitud de afiliacion por formulario publico',
                'datos_nuevos' => [
                    'cedula' => $submitted['cedula'] ?? null,
                    'correo' => $submitted['correo'] ?? null
                ],
                'resultado' => 'exitoso'
            ]);
            $this->notifySebana($submitted);
        }

        unset($_SESSION['afiliacion_form_data'], $_SESSION['afiliacion_draft_id']);
        $this->redirect('/SGA-SEBANA/public/afiliarse?success=enviado');
    }

    public function index()
    {
        if (!$this->requireManager()) {
            return;
        }

        $this->view('index', [
            'title' => 'Solicitudes de Afiliacion',
            'solicitudes' => $this->model->listAll(),
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function show($id)
    {
        if (!$this->requireManager()) {
            return;
        }

        $solicitud = $this->model->read($id);
        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes?error=not_found');
            return;
        }

        $this->view('show', [
            'title' => 'Detalle Solicitud de Afiliacion',
            'solicitud' => $solicitud,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null,
            'flash_success' => $this->getFlash('afiliacion_admin_success'),
            'flash_error' => $this->getFlash('afiliacion_admin_error')
        ]);
    }

    public function updateStatus($id)
    {
        if (!$this->requireManager()) {
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes/' . $id);
            return;
        }

        $estado = $_POST['nuevo_estado'] ?? '';
        $observaciones = trim((string) ($_POST['observaciones_admin'] ?? ''));
        $userId = $this->getCurrentUserId();
        $solicitud = $this->model->read($id);

        if (!$solicitud) {
            $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes?error=not_found');
            return;
        }

        $estadoNormalizado = strtolower(trim((string) $estado));
        $approvalSync = null;

        if ($estadoNormalizado === 'aprobada') {
            $approvalSync = $this->ensureAffiliateAndUserForApproval($solicitud);
            if (!($approvalSync['success'] ?? false)) {
                $_SESSION['afiliacion_admin_error'] = $approvalSync['error'] ?? 'No se pudo completar la aprobacion automatica.';
                $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes/' . $id . '?error=estado');
                return;
            }
        }

        if ($this->model->updateStatus($id, $estado, $observaciones, $userId)) {
            $this->logBitacora([
                'accion' => 'STATUS_CHANGE',
                'modulo' => 'asistente_afiliacion',
                'entidad' => 'solicitud_afiliacion',
                'entidad_id' => (int) $id,
                'descripcion' => "Cambio de estado de solicitud de afiliacion a {$estado}",
                'datos_nuevos' => ['estado' => $estado, 'observaciones_admin' => $observaciones],
                'resultado' => 'exitoso'
            ]);
            if ($approvalSync !== null) {
                $_SESSION['afiliacion_admin_success'] = $this->buildApprovalProvisionMessage($approvalSync);
            }
            $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes/' . $id . '?success=estado');
            return;
        }

        $this->logBitacora([
            'accion' => 'STATUS_CHANGE',
            'modulo' => 'asistente_afiliacion',
            'entidad' => 'solicitud_afiliacion',
            'entidad_id' => (int) $id,
            'descripcion' => 'Error al actualizar estado de solicitud de afiliacion',
            'resultado' => 'fallido',
            'mensaje_error' => $this->model->getLastError()
        ]);
        $_SESSION['afiliacion_admin_error'] = 'No se pudo actualizar el estado de la solicitud.';
        $this->redirect('/SGA-SEBANA/public/asistente-afiliacion/solicitudes/' . $id . '?error=estado');
    }

    public function documento($id, $tipo)
    {
        if (!$this->requireManager()) {
            return;
        }

        $solicitud = $this->model->read($id);
        if (!$solicitud) {
            http_response_code(404);
            echo 'Solicitud no encontrada.';
            return;
        }

        $tipo = strtolower(trim((string) $tipo));
        $relativePath = null;
        $downloadName = 'documento.pdf';

        if ($tipo === 'generado') {
            $relativePath = $solicitud['pdf_generado_path'] ?? null;
            $downloadName = 'Formulario_Afiliacion_' . ($solicitud['id'] ?? $id) . '.pdf';
        } elseif ($tipo === 'firmado') {
            $relativePath = $solicitud['pdf_firmado_path'] ?? null;
            $downloadName = 'Formulario_Afiliacion_Firmado_' . ($solicitud['id'] ?? $id) . '.pdf';
        }

        $absolute = $this->model->buildAbsolutePath($relativePath);
        if (!$absolute) {
            http_response_code(404);
            echo 'Archivo no encontrado.';
            return;
        }

        $mime = function_exists('mime_content_type') ? mime_content_type($absolute) : 'application/pdf';
        $disposition = isset($_GET['download']) ? 'attachment' : 'inline';

        header('Content-Type: ' . ($mime ?: 'application/pdf'));
        header('Content-Length: ' . filesize($absolute));
        header('Content-Disposition: ' . $disposition . '; filename="' . $downloadName . '"');
        readfile($absolute);
        exit;
    }
}
