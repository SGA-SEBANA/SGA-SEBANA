<?php

namespace App\Modules\CasosRRLL\Controllers;

use App\Core\ControllerBase;
use App\Modules\CasosRRLL\Models\CasosRRLL;
use App\Modules\CasosRRLL\Models\Etapas;
use App\Modules\CasosRRLL\Services\CasosRRLLService;
use App\Modules\Usuarios\Models\Bitacora;
use Dompdf\Dompdf;
use Dompdf\Options;

class CasosRRLLController extends ControllerBase
{
    private $modelo_casos;
    private $modelo_etapas;
    private $bitacora;
    private $service;

    public function __construct()
    {
        $this->modelo_casos = new CasosRRLL();
        $this->modelo_etapas = new Etapas();
        $this->bitacora = new Bitacora();
        $this->service = new CasosRRLLService();
    }

    // ========================================
    // MÉTODOS PARA CASOS RELACIONES LABORALES
    // ========================================

    /**
     * Listado de casos con filtros
     */
    public function index()
    {
        $filtros = [
            'busqueda' => trim($_GET['q'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'afiliado_id' => $_GET['afiliado_id'] ?? '',
            'etapa_nombre' => trim($_GET['etapa_nombre'] ?? ''),
            'etapa_estado' => $_GET['etapa_estado'] ?? '',
            'responsable_id' => $_GET['responsable_id'] ?? '',
            'con_documentos' => $_GET['con_documentos'] ?? '',
            'atraso' => $_GET['atraso'] ?? ''
        ];

        if (($filtros['etapa_nombre'] ?? '') === '' && (($_GET['solo_investigacion'] ?? '') === '1')) {
            $filtros['etapa_nombre'] = 'investigacion';
        }

        $casos = $this->modelo_casos->getAll($filtros);
        $categorias = $this->modelo_casos->getCategorias();
        $afiliados = $this->modelo_casos->getAfiliados();
        $usuarios = $this->modelo_casos->getUsuarios();

        // Agregar métricas derivadas para DataGrid
        foreach ($casos as &$caso) {
            $total_etapas = $this->modelo_casos->contarTotalEtapas($caso['id']);
            $etapas_finalizadas = $this->modelo_casos->contarEtapasFinalizadas($caso['id']);
            $caso['progreso'] = $total_etapas > 0 ? round(($etapas_finalizadas / $total_etapas) * 100) : 0;
            $caso['total_etapas'] = $total_etapas;

            $fechaEstimada = $caso['etapa_actual_fecha_estimada'] ?? null;
            $etapaEstado = $caso['etapa_actual_estado'] ?? null;
            $caso['semaforo_atraso'] = 'sin_fecha';

            if (!empty($fechaEstimada) && in_array($etapaEstado, ['pendiente', 'en_progreso', 'bloqueado'], true)) {
                $hoy = new \DateTimeImmutable('today');
                $fEst = \DateTimeImmutable::createFromFormat('Y-m-d', (string) $fechaEstimada) ?: new \DateTimeImmutable((string) $fechaEstimada);
                $diff = (int) $fEst->diff($hoy)->format('%r%a');

                if ($diff > 5) {
                    $caso['semaforo_atraso'] = 'rojo';
                } elseif ($diff > 0) {
                    $caso['semaforo_atraso'] = 'amarillo';
                } else {
                    $caso['semaforo_atraso'] = 'verde';
                }
            }
        }
        unset($caso);

        $data = [
            'titulo' => 'Gestión de Casos - Relaciones Laborales',
            'casos' => $casos,
            'categorias' => $categorias,
            'afiliados' => $afiliados,
            'usuarios' => $usuarios,
            'filtros' => $filtros,
            'success' => $_GET['success'] ?? null,
            'error_msg' => $_GET['error'] ?? null
        ];

        $this->view('index', $data);
    }

    /**
     * Vista crear caso
     */
    public function create()
    {
        $numero_expediente = $this->modelo_casos->generarNumeroExpediente();

        $data = [
            'titulo' => 'Registrar Nuevo Caso de Relaciones Laborales',
            'numero_expediente' => $numero_expediente,
            'categorias' => $this->modelo_casos->getCategorias(),
            'afiliados' => $this->modelo_casos->getAfiliados(),
            'usuarios' => $this->modelo_casos->getUsuarios(),
            'error_msg' => $_GET['error'] ?? null
        ];

        $this->view('create', $data);
    }

    /**
     * Guardar nuevo caso
     */
    public function store()
    {
        $datos = $this->limpiarDatos($_POST);

        // Validaciones
        if (empty($datos['titulo']) || empty($datos['categoria_id'])) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll/create?error=campos_requeridos');
            return;
        }

        if ($this->modelo_casos->existeExpediente($datos['numero_expediente'])) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll/create?error=expediente_duplicado');
            return;
        }

        $result = $this->service->crearCaso($datos);
        if (($result['ok'] ?? false) === true) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?success=creado');
            return;
        }

        $this->redirect('/SGA-SEBANA/public/casos-rrll/create?error=db_error');
    }

    /**
     * Ver detalles del caso
     */
    public function show($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        // Obtener etapas y progreso
        $etapas = $this->modelo_etapas->getEtapasCaso($id);
        $progreso = $this->modelo_etapas->getProgresoCaso($id);
        $historial = $this->modelo_casos->obtenerHistorialCaso($id, 200);
        $documentosCaso = $this->modelo_casos->obtenerDocumentosCaso($id);

        $documentosEtapas = [];
        foreach ($etapas as $etapaItem) {
            $docs = $this->modelo_etapas->obtenerDocumentosEtapa((int) $etapaItem['id']);
            foreach ($docs as $doc) {
                $doc['etapa_id'] = $etapaItem['id'];
                $doc['etapa_nombre'] = $etapaItem['nombre'] ?? ('Etapa #' . $etapaItem['id']);
                $documentosEtapas[] = $doc;
            }
        }

        $etapaActual = $this->modelo_casos->obtenerEtapaActual($id);

        $data = [
            'titulo' => 'Detalles del Caso: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'etapas' => $etapas,
            'progreso' => $progreso,
            'usuarios' => $this->modelo_casos->getUsuarios(),
            'historial' => $historial,
            'documentosCaso' => $documentosCaso,
            'documentosEtapas' => $documentosEtapas,
            'etapaActual' => $etapaActual
        ];

        $this->view('show', $data);
    }

    /**
     * Editar caso
     */
    public function edit($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $data = [
            'titulo' => 'Editar Caso: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'categorias' => $this->modelo_casos->getCategorias(),
            'afiliados' => $this->modelo_casos->getAfiliados(),
            'usuarios' => $this->modelo_casos->getUsuarios(),
            'error_msg' => $_GET['error'] ?? null
        ];

        $this->view('edit', $data);
    }

    /**
     * Actualizar caso
     */
    public function update($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $datos = $this->limpiarDatos($_POST);

        // Validaciones
        if (empty($datos['titulo']) || empty($datos['categoria_id'])) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/edit/{$id}?error=campos_requeridos");
            return;
        }

        // Verificar expediente duplicado (excepto el actual)
        if ($this->modelo_casos->existeExpediente($datos['numero_expediente'], $id)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/edit/{$id}?error=expediente_duplicado");
            return;
        }

        $result = $this->service->actualizarCasoFijo((int) $id, $datos);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?success=actualizado");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/edit/{$id}?error=db_error");
    }

    /**
     * Cambiar estado del caso
     */
    public function cambiarEstado($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso || !isset($_POST['nuevo_estado'])) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=solicitud_invalida');
            return;
        }

        $nuevo_estado = $_POST['nuevo_estado'];
        $resultadoFinal = isset($_POST['resultado_final']) ? trim((string) $_POST['resultado_final']) : null;
        $result = $this->service->cambiarEstadoCaso((int) $id, (string) $nuevo_estado, $resultadoFinal);

        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?success=estado_actualizado");
            return;
        }

        $error = $result['error'] ?? 'estado_invalido';
        $map = [
            'transicion_invalida' => 'Transicion de estado invalida para este expediente.',
            'etapas_activas' => 'No puede cerrar el expediente mientras existan etapas activas.',
            'resultado_requerido' => 'Debe registrar el resultado final para cerrar el expediente.',
            'db_error' => 'Hubo un error al actualizar el estado del expediente.'
        ];
        $msg = $map[$error] ?? 'No se pudo actualizar el estado del expediente.';
        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?error=" . urlencode($msg));
    }

    /**
     * Cambiar responsable del caso
     */
    public function cambiarResponsable($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}");
            return;
        }

        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $responsable_actual = $_POST['responsable_actual'] ?? null;
        $result = $this->service->cambiarResponsable((int) $id, $responsable_actual);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?success=responsable_actualizado");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?error=db_error");
    }

    /**
     * Archivar caso
     */
    public function archivar($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $resultado = $_POST['resultado_final'] ?? null;
        $result = $this->service->archivarCaso((int) $id, $resultado);
        if (($result['ok'] ?? false) === true) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?success=archivado');
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?error=db_error");
    }

    /**
     * Eliminar caso
     */
    public function delete($id)
    {
        $caso = $this->modelo_casos->getById($id);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $result = $this->service->eliminarCasoLogico((int) $id);
        if (($result['ok'] ?? false) === true) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?success=archivado');
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?error=db_error");
    }

    // ========================================
    // MÉTODOS PARA ETAPAS DE CASOS
    // ========================================

    /**
     * Listado de etapas de un caso
     */
    public function etapas($casoId)
    {
        $caso = $this->modelo_casos->getById($casoId);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $etapas = $this->modelo_etapas->getEtapasCaso($casoId);
        $progreso = $this->modelo_etapas->getProgresoCaso($casoId);

        $data = [
            'titulo' => 'Etapas del Caso: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'etapas' => $etapas,
            'progreso' => $progreso,
            'success' => $_GET['success'] ?? null,
            'error_msg' => $_GET['error'] ?? null
        ];

        $this->view('etapas/index', $data);
    }

    /**
     * Vista crear etapa
     */
    public function crearEtapa($casoId)
    {
        $caso = $this->modelo_casos->getById($casoId);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $data = [
            'titulo' => 'Nueva Etapa: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'usuarios' => $this->modelo_etapas->getUsuarios()
        ];

        $this->view('etapas/create', $data);
    }

    /**
     * Guardar nueva etapa
     */
    public function guardarEtapa($casoId)
    {
        $caso = $this->modelo_casos->getById($casoId);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $datos = $this->limpiarDatos($_POST);
        if (empty($datos['nombre'])) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=campos_requeridos");
            return;
        }

        if ($this->modelo_etapas->existeNombreEnCaso($datos['nombre'], $casoId)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=nombre_duplicado");
            return;
        }

        $result = $this->service->crearEtapa((int) $casoId, $datos);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?success=etapa_creada");
            return;
        }

        if (($result['error'] ?? '') === 'orden_duplicado') {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=orden_duplicado");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=db_error");
    }

    /**
     * Editar etapa
     */
    public function editarEtapa($etapaId)
    {
        $etapa = $this->modelo_etapas->getById($etapaId);

        if (!$etapa) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $data = [
            'titulo' => 'Editar Etapa: ' . $etapa['nombre'],
            'etapa' => $etapa,
            'usuarios' => $this->modelo_etapas->getUsuarios()
        ];

        $this->view('etapas/edit', $data);
    }

    /**
     * Actualizar etapa
     */
    public function actualizarEtapa($etapaId)
    {
        $etapa = $this->modelo_etapas->getById($etapaId);

        if (!$etapa) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $datos = $this->limpiarDatos($_POST);

        // Validaciones
        if (empty($datos['nombre'])) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=campos_requeridos");
            return;
        }

        // Verificar nombre duplicado (excepto el actual)
        if ($this->modelo_etapas->existeNombreEnCaso($datos['nombre'], $etapa['caso_id'], $etapaId)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=nombre_duplicado");
            return;
        }

        $result = $this->service->actualizarEtapa((int) $etapaId, $datos);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?success=etapa_actualizada");
            return;
        }

        if (($result['error'] ?? '') === 'orden_duplicado') {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=orden_duplicado");
            return;
        }
        if (($result['error'] ?? '') === 'orden_saltado') {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=orden_saltado");
            return;
        }
        if (($result['error'] ?? '') === 'fecha_real_requerida') {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=fecha_real_requerida");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/etapas/{$etapaId}/edit?error=db_error");
    }

    /**
     * Cambiar estado de etapa
     */
    public function cambiarEstadoEtapa($etapaId)
    {
        $etapa = $this->modelo_etapas->getById($etapaId);

        if (!$etapa || !isset($_POST['nuevo_estado'])) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=solicitud_invalida');
            return;
        }

        $nuevo_estado = $_POST['nuevo_estado'];
        $fechaReal = $_POST['fecha_real'] ?? null;
        $result = $this->service->cambiarEstadoEtapa((int) $etapaId, (string) $nuevo_estado, $fechaReal);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?success=etapa_actualizada");
            return;
        }

        $error = $result['error'] ?? 'estado_invalido';
        $map = [
            'orden_saltado' => 'No puede avanzar/finalizar esta etapa hasta completar las anteriores.',
            'fecha_real_requerida' => 'Debe indicar la fecha real de finalizacion.',
            'db_error' => 'No fue posible actualizar la etapa.'
        ];
        $msg = $map[$error] ?? 'No fue posible actualizar la etapa.';
        $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?error=" . urlencode($msg));
    }

    /**
     * Historial de etapas
     */
    public function historialEtapas($casoId)
    {
        $caso = $this->modelo_casos->getById($casoId);

        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $historial = $this->modelo_etapas->getHistorialEtapas($casoId);

        $data = [
            'titulo' => 'Historial de Etapas: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'historial' => $historial
        ];

        $this->view('etapas/historial', $data);
    }

    /**
     * Eliminar etapa
     */
    public function eliminarEtapa($etapaId)
    {
        $etapa = $this->modelo_etapas->getById($etapaId);

        if (!$etapa) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        $casoId = $etapa['caso_id'];

        $result = $this->service->eliminarEtapaLogico((int) $etapaId);
        if (($result['ok'] ?? false) === true) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?success=etapa_eliminada");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?error=db_error");
    }

    /**
     * Adjuntar documento al caso o a una etapa del caso
     */
    public function adjuntarDocumento($casoId)
    {
        $caso = $this->modelo_casos->getById($casoId);
        if (!$caso) {
            $this->redirect('/SGA-SEBANA/public/casos-rrll?error=no_encontrado');
            return;
        }

        if (!isset($_FILES['documento']) || !is_array($_FILES['documento'])) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('Debe adjuntar un archivo valido.'));
            return;
        }

        $upload = $_FILES['documento'];
        if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('No fue posible subir el archivo.'));
            return;
        }

        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
        $originalName = (string) ($upload['name'] ?? '');
        $tmp = (string) ($upload['tmp_name'] ?? '');
        $size = (int) ($upload['size'] ?? 0);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('Tipo de archivo no permitido.'));
            return;
        }
        if ($size <= 0 || $size > (10 * 1024 * 1024)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('El archivo supera el tamano permitido (10MB).'));
            return;
        }

        $storageDir = BASE_PATH . '/storage/casos_rrll/documentos';
        if (!is_dir($storageDir) && !mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('No se pudo preparar el almacenamiento local.'));
            return;
        }

        try {
            $randomPart = bin2hex(random_bytes(4));
        } catch (\Throwable $e) {
            $randomPart = substr(md5((string) microtime(true)), 0, 8);
        }
        $safeName = 'rrll_' . (int) $casoId . '_' . time() . '_' . $randomPart . '.' . $ext;
        $target = $storageDir . '/' . $safeName;
        if (!move_uploaded_file($tmp, $target)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('No se pudo guardar el archivo en storage.'));
            return;
        }

        $etapaId = isset($_POST['etapa_id']) && (int) $_POST['etapa_id'] > 0 ? (int) $_POST['etapa_id'] : null;
        $documento = [
            'nombre_original' => $originalName,
            'ruta' => 'storage/casos_rrll/documentos/' . $safeName,
            'tamano' => $size,
            'mime' => mime_content_type($target) ?: null,
            'fecha' => date('Y-m-d H:i:s'),
            'subido_por' => $_SESSION['usuario_id'] ?? null
        ];

        $result = $this->service->adjuntarDocumento((int) $casoId, $etapaId, $documento);
        if (($result['ok'] ?? false) !== true) {
            if (file_exists($target)) {
                @unlink($target);
            }
            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?error=" . urlencode('No se pudo registrar el adjunto en el expediente.'));
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$casoId}?success=documento_subido");
    }

    // ========================================
    // REPORTES Y EXPORTACIÓN
    // ========================================

    /**
     * Exportar casos a PDF
     */
    public function exportarPDF()
    {
        $filtros = [
            'estado' => $_GET['estado'] ?? '',
            'categoria_id' => $_GET['categoria_id'] ?? '',
            'prioridad' => $_GET['prioridad'] ?? '',
            'etapa_nombre' => trim($_GET['etapa_nombre'] ?? ''),
            'etapa_estado' => $_GET['etapa_estado'] ?? ''
        ];

        if (($filtros['etapa_nombre'] ?? '') === '' && (($_GET['solo_investigacion'] ?? '') === '1')) {
            $filtros['etapa_nombre'] = 'investigacion';
        }

        $casos = $this->modelo_casos->getAll($filtros);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = '<h2 style="text-align:center;">Reporte de Casos - Relaciones Laborales</h2>';
        $html .= '<p style="text-align:center; color:#666;">Generado: ' . date('d/m/Y H:i') . '</p>';
        $html .= '<table border="1" cellspacing="0" cellpadding="8" width="100%">';
        $html .= '<thead style="background-color:#f0f0f0;"><tr>
                    <th>Expediente</th>
                    <th>Título</th>
                    <th>Afiliado</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Fecha Apertura</th>
                  </tr></thead><tbody>';

        if (!empty($casos)) {
            foreach ($casos as $caso) {
                $html .= '<tr>
                            <td>' . htmlspecialchars($caso['numero_expediente']) . '</td>
                            <td>' . htmlspecialchars($caso['titulo']) . '</td>
                            <td>' . htmlspecialchars($caso['afiliado_nombre'] ?? 'N/A') . '</td>
                            <td>' . ucfirst($caso['estado']) . '</td>
                            <td>' . ucfirst($caso['prioridad']) . '</td>
                            <td>' . date('d/m/Y', strtotime($caso['fecha_apertura'])) . '</td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="6" style="text-align:center; color:#999;">No hay casos para mostrar</td></tr>';
        }

        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $this->bitacora->log([
            'accion' => 'READ',
            'modulo' => 'casos_rrll',
            'entidad' => 'reporte',
            'descripcion' => 'Exportación de casos a PDF'
        ]);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="casos_rrll_' . date('Ymd_His') . '.pdf"');
        echo $dompdf->output();
        exit;
    }

    /**
     * Limpiar y procesar datos del formulario
     */
    private function limpiarDatos($post)
    {
        // Array base con todos los campos posibles
        $datos = [];

        // Campos comunes para casos
        $camposCasos = [
            'numero_expediente', 'titulo', 'descripcion', 'hechos', 'empresa_involucrada',
            'departamento_afectado', 'observaciones', 'afiliado_id', 'categoria_id',
            'responsable_actual', 'prioridad', 'estado', 'fecha_incidente', 'fecha_apertura'
        ];

        // Campos para etapas
        $camposEtapas = [
            'nombre', 'descripcion', 'estado', 'fecha_inicio', 'fecha_estimada_fin',
            'fecha_fin', 'responsable_id', 'resultado', 'documentos_generados',
            'observaciones', 'orden'
        ];

        // Procesar campos de casos
        foreach ($camposCasos as $campo) {
            if (isset($post[$campo])) {
                $valor = $post[$campo];
                // Convertir valores vacíos a null, excepto para cadenas que se dejan vacías
                if ($campo === 'afiliado_id' || $campo === 'responsable_actual' || $campo === 'categoria_id') {
                    $datos[$campo] = (empty($valor)) ? null : trim($valor);
                } else {
                    $datos[$campo] = trim($valor);
                }
            }
        }

        // Procesar campos de etapas
        foreach ($camposEtapas as $campo) {
            if (isset($post[$campo])) {
                $valor = $post[$campo];
                if ($campo === 'responsable_id' || $campo === 'orden') {
                    $datos[$campo] = (empty($valor)) ? null : trim($valor);
                } else {
                    $datos[$campo] = trim($valor);
                }
            }
        }

        // Agregar creado_por desde sesión si no está presente
        if (!isset($datos['creado_por']) && isset($_SESSION['usuario_id'])) {
            $datos['creado_por'] = $_SESSION['usuario_id'];
        }

        return $datos;
    }
}
