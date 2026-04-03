<?php

namespace App\Modules\CasosRRLL\Controllers;

use App\Core\ControllerBase;
use App\Modules\CasosRRLL\Models\CasosRRLL;
use App\Modules\CasosRRLL\Models\Etapas;
use App\Modules\Usuarios\Models\Bitacora;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Modules\Visitas\Models\Notification; 

class CasosRRLLController extends ControllerBase
{
    private $modelo_casos;
    private $modelo_etapas;
    private $bitacora;
    private $notiModel;

    public function __construct()
    {
        $this->modelo_casos = new CasosRRLL();
        $this->modelo_etapas = new Etapas();
        $this->bitacora = new Bitacora();
        $this->notiModel = new Notification(); 
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
            'afiliado_id' => $_GET['afiliado_id'] ?? ''
        ];

        $casos = $this->modelo_casos->getAll($filtros);
        $categorias = $this->modelo_casos->getCategorias();
        $afiliados = $this->modelo_casos->getAfiliados();

        // Agregar progreso a cada caso
        foreach ($casos as &$caso) {
            $total_etapas = $this->modelo_casos->contarTotalEtapas($caso['id']);
            $etapas_finalizadas = $this->modelo_casos->contarEtapasFinalizadas($caso['id']);
            $caso['progreso'] = $total_etapas > 0 ? round(($etapas_finalizadas / $total_etapas) * 100) : 0;
            $caso['total_etapas'] = $total_etapas;
        }

        $data = [
            'titulo' => 'Gestión de Casos - Relaciones Laborales',
            'casos' => $casos,
            'categorias' => $categorias,
            'afiliados' => $afiliados,
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

        if ($this->modelo_casos->create($datos)) {
            $this->bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'descripcion' => "Creación de caso: {$datos['numero_expediente']}"
            ]);


            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Nuevo Caso RRLL',
                "Se creó el caso con expediente {$datos['numero_expediente']}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$datos['numero_expediente']}"
            );


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

        $data = [
            'titulo' => 'Detalles del Caso: ' . $caso['numero_expediente'],
            'caso' => $caso,
            'etapas' => $etapas,
            'progreso' => $progreso,
            'usuarios' => $this->modelo_casos->getUsuarios()
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
            'usuarios' => $this->modelo_casos->getUsuarios()
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

        if ($this->modelo_casos->update($id, $datos)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Actualización de caso: {$datos['numero_expediente']}"
            ]);


            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Caso RRLL Actualizado',
                "Se actualizó el caso ID {$id}, expediente {$datos['numero_expediente']}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );


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

        if ($this->modelo_casos->cambiarEstado($id, $nuevo_estado)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Cambio de estado del caso a: {$nuevo_estado}"
            ]);

            $this->notiModel->createNotification(
            1,
            'sistema',
            'casos',
            'Estado de Etapa Cambiado',
            "La etapa ID {$etapaId} ahora está en estado: {$nuevo_estado}",
            'etapa_rrll',
            $etapaId,
            "/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas"
        );



            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Estado de Caso Cambiado',
                "El caso ID {$id} ahora está en estado: {$nuevo_estado}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );


            $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?success=estado_actualizado");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/show/{$id}?error=estado_invalido");
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

        if ($this->modelo_casos->cambiarResponsable($id, $responsable_actual)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Cambio de responsable del caso"
            ]);


            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Responsable de Caso Actualizado',
                "Se cambió el responsable del caso ID {$id}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );


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

        if ($this->modelo_casos->archivar($id, $resultado)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Archivación de caso: {$caso['numero_expediente']}"
            ]);


            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Caso Archivado',
                "Se archivó el caso ID {$id}, expediente {$caso['numero_expediente']}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );


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

        if ($this->modelo_casos->delete($id)) {
            $this->bitacora->log([
                'accion' => 'DELETE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Eliminación de caso: {$caso['numero_expediente']}"
            ]);


            // Notificación
            $this->notiModel->createNotification(
                1,
                'sistema',
                'casos',
                'Caso Eliminado',
                "Se eliminó el caso ID {$id}, expediente {$caso['numero_expediente']}",
                'caso_rrll',
                $id,
                null
            );


            $this->redirect('/SGA-SEBANA/public/casos-rrll?success=eliminado');
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
        $datos['caso_id'] = $casoId;

        // Validaciones
        if (empty($datos['nombre'])) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=campos_requeridos");
            return;
        }

        if ($this->modelo_etapas->existeNombreEnCaso($datos['nombre'], $casoId)) {
            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas/create?error=nombre_duplicado");
            return;
        }

        if ($this->modelo_etapas->create($datos)) {
            $this->bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'descripcion' => "Creación de etapa: {$datos['nombre']} para caso {$casoId}"
            ]);

            $this->notiModel->createNotification(
            1,
            'sistema',
            'casos',
            'Nueva Etapa en Caso RRLL',
            "Se creó la etapa '{$datos['nombre']}' en el caso ID {$casoId}",
            'etapa_rrll',
            $etapaId,
            "/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas"
        );


            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?success=etapa_creada");
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

        if ($this->modelo_etapas->update($etapaId, $datos)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Actualización de etapa: {$datos['nombre']}"
            ]);

             $this->notiModel->createNotification(
            1,
            'sistema',
            'casos',
            'Etapa Actualizada',
            "Se actualizó la etapa '{$datos['nombre']}' del caso ID {$etapa['caso_id']}",
            'etapa_rrll',
            $etapaId,
            "/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas"
        );



            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?success=etapa_actualizada");
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

        if ($this->modelo_etapas->cambiarEstado($etapaId, $nuevo_estado)) {
            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Cambio de estado de etapa a: {$nuevo_estado}"
            ]);

            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?success=etapa_actualizada");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/{$etapa['caso_id']}/etapas?error=estado_invalido");
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

        if ($this->modelo_etapas->delete($etapaId)) {
            $this->bitacora->log([
                'accion' => 'DELETE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Eliminación de etapa: {$etapa['nombre']}"
            ]);

            $this->notiModel->createNotification(
            1,
            'sistema',
            'casos',
            'Etapa Eliminada',
            "Se eliminó la etapa '{$etapa['nombre']}' del caso ID {$casoId}",
            'etapa_rrll',
            $etapaId,
            "/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas"
        );


            $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?success=etapa_eliminada");
            return;
        }

        $this->redirect("/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas?error=db_error");
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
            'prioridad' => $_GET['prioridad'] ?? ''
        ];

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
            'responsable_id', 'observaciones', 'orden'
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
