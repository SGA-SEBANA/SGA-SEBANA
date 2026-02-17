<?php

namespace App\Modules\Puestos\Controllers;

use App\Modules\Puestos\Models\PuestosModel;
use App\Modules\Usuarios\Models\Bitacora;
use Dompdf\Dompdf;

class PuestosController
{
    /**
     * List all puestos with optional filters
     */
    public function index()
    {
        $model = new PuestosModel();

        $filtros = [
            'busqueda' => trim($_GET['q'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'afiliado_id' => $_GET['afiliado_id'] ?? '',
        ];

        $puestos = $model->getAll($filtros);
        $afiliados = $model->getAfiliados();

        $title = "Gestión de Puestos";
        $success = $_GET['success'] ?? null;

        require BASE_PATH . '/app/modules/Puestos/Views/index.php';
    }

    /**
     * Show create form (GET) or store new puesto (POST)
     */
    public function create()
    {
        $model = new PuestosModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->limpiarDatos($_POST);

            // Validate required fields
            if (empty($data['afiliado_id']) || empty($data['nombre']) || empty($data['fecha_asignacion'])) {
                echo "<script>alert('Error: Afiliado, Nombre del Puesto y Fecha de Asignación son obligatorios.'); window.history.back();</script>";
                return;
            }

            $newId = $model->createPuesto($data);

            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'puestos',
                'entidad' => 'puesto',
                'entidad_id' => $newId,
                'descripcion' => "Asignación de puesto '{$data['nombre']}' a afiliado ID: {$data['afiliado_id']}",
                'datos_nuevos' => $data,
            ]);

            header("Location: /SGA-SEBANA/public/puestos?success=Puesto asignado correctamente");
            exit;
        }

        // GET: show form
        $afiliados = $model->getAfiliados();
        $oficinas = $model->getOficinas();
        $usuarios = $model->getUsuarios();
        $title = "Asignar Nuevo Puesto";

        require BASE_PATH . '/app/modules/Puestos/Views/create.php';
    }

    /**
     * Show edit form (GET) or update puesto (POST)
     */
    public function edit($id)
    {
        $model = new PuestosModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->limpiarDatos($_POST);

            // Get previous data for log
            $anterior = $model->getById($id);

            if (empty($data['nombre']) || empty($data['fecha_asignacion'])) {
                echo "<script>alert('Error: Nombre del Puesto y Fecha de Asignación son obligatorios.'); window.history.back();</script>";
                return;
            }

            $model->updatePuesto($id, $data);

            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'puestos',
                'entidad' => 'puesto',
                'entidad_id' => $id,
                'descripcion' => "Actualización de puesto ID: {$id}",
                'datos_anteriores' => $anterior,
                'datos_nuevos' => $data,
            ]);

            header("Location: /SGA-SEBANA/public/puestos?success=Puesto actualizado correctamente");
            exit;
        }

        // GET: show form
        $puesto = $model->getById($id);
        if (!$puesto) {
            echo "Puesto no encontrado.";
            return;
        }

        $afiliados = $model->getAfiliados();
        $oficinas = $model->getOficinas();
        $usuarios = $model->getUsuarios();
        $title = "Editar Puesto";

        require BASE_PATH . '/app/modules/Puestos/Views/edit.php';
    }

    /**
     * Delete a puesto
     */
    public function delete($id)
    {
        $model = new PuestosModel();

        $puesto = $model->getById($id);
        if (!$puesto) {
            echo "Puesto no encontrado.";
            return;
        }

        $model->deletePuesto($id);

        // Log Bitacora
        $bitacora = new Bitacora();
        $bitacora->log([
            'accion' => 'DELETE',
            'modulo' => 'puestos',
            'entidad' => 'puesto',
            'entidad_id' => $id,
            'descripcion' => "Eliminación de puesto '{$puesto['nombre']}' (afiliado ID: {$puesto['afiliado_id']})",
            'datos_anteriores' => $puesto,
        ]);

        header("Location: /SGA-SEBANA/public/puestos?success=Puesto eliminado correctamente");
        exit;
    }

    /**
     * Toggle puesto estado (activo <-> finalizado)
     */
    public function toggle($id)
    {
        $model = new PuestosModel();

        $puesto = $model->getById($id);
        if (!$puesto) {
            echo "Puesto no encontrado.";
            return;
        }

        // Toggle: activo -> finalizado, anything else -> activo
        $nuevoEstado = ($puesto['estado'] === 'activo') ? 'finalizado' : 'activo';

        $model->toggleEstado($id, $nuevoEstado);

        // Log Bitacora
        $bitacora = new Bitacora();
        $estadoLabel = $nuevoEstado === 'activo' ? 'Activado' : 'Finalizado';
        $bitacora->log([
            'accion' => 'UPDATE',
            'modulo' => 'puestos',
            'entidad' => 'puesto',
            'entidad_id' => $id,
            'descripcion' => "Cambio de estado del puesto '{$puesto['nombre']}' a '{$nuevoEstado}'",
            'datos_anteriores' => ['estado' => $puesto['estado']],
            'datos_nuevos' => ['estado' => $nuevoEstado],
        ]);

        header("Location: /SGA-SEBANA/public/puestos?success=Estado del puesto actualizado a '{$estadoLabel}'");
        exit;
    }

    /**
     * View all puestos for a specific afiliado (history)
     */
    public function historial($afiliadoId)
    {
        $model = new PuestosModel();

        $afiliado = $model->getAfiliadoById($afiliadoId);
        if (!$afiliado) {
            echo "Afiliado no encontrado.";
            return;
        }

        $puestos = $model->getByAfiliado($afiliadoId);
        $title = "Historial de Puestos - " . $afiliado['nombre_completo'];

        require BASE_PATH . '/app/modules/Puestos/Views/historial.php';
    }

    /**
     * Report view with statistics
     */
    public function reportes()
    {
        $model = new PuestosModel();

        $estadisticas = $model->getEstadisticas();
        $puestos = $model->getAll();
        $title = "Reportes de Puestos";

        require BASE_PATH . '/app/modules/Puestos/Views/reportes.php';
    }

    /**
     * Export puestos to CSV
     */
    public function exportCsv()
    {
        $model = new PuestosModel();

        $filtros = [
            'busqueda' => trim($_GET['q'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'afiliado_id' => $_GET['afiliado_id'] ?? '',
        ];

        $puestos = $model->getAll($filtros);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_puestos_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');

        // BOM for Excel UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($output, [
            'ID',
            'Afiliado',
            'Cédula',
            'Puesto',
            'Departamento',
            'Oficina',
            'Tipo Contrato',
            'Jornada',
            'Salario Base',
            'Estado',
            'Fecha Asignación',
            'Fecha Remoción',
            'Motivo Remoción',
            'Asignado Por',
            'Observaciones'
        ]);

        foreach ($puestos as $p) {
            // Force cedula as text to prevent Excel scientific notation
            $cedula = '="' . ($p['afiliado_cedula'] ?? '') . '"';
            // Format dates as text to prevent Excel '#' display
            $fechaAsignacion = !empty($p['fecha_asignacion']) ? '="' . date('Y-m-d', strtotime($p['fecha_asignacion'])) . '"' : '';
            $fechaRemocion = !empty($p['fecha_remocion']) ? '="' . date('Y-m-d', strtotime($p['fecha_remocion'])) . '"' : '';

            fputcsv($output, [
                $p['id'],
                $p['afiliado_nombre'],
                $cedula,
                $p['nombre'],
                $p['departamento'] ?? '',
                $p['oficina_nombre'] ?? '',
                $p['tipo_contrato'],
                $p['jornada'],
                $p['salario_base'] ?? '',
                $p['estado'],
                $fechaAsignacion,
                $fechaRemocion,
                $p['motivo_remocion'] ?? '',
                $p['asignado_por_nombre'] ?? '',
                $p['observaciones'] ?? '',
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export puestos to PDF (HTML printable view)
     */
    public function exportPdf()
    {
        $model = new PuestosModel();

        $filtros = [
            'busqueda' => trim($_GET['q'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'afiliado_id' => $_GET['afiliado_id'] ?? '',
        ];

        $puestos = $model->getAll($filtros);
        $estadisticas = $model->getEstadisticas();

        // Render HTML template into string
        ob_start();
        require BASE_PATH . '/app/modules/Puestos/Views/export_pdf.php';
        $html = ob_get_clean();

        // Generate PDF with dompdf
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('reporte_puestos_' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
    }

    /**
     * Clean and normalize form data
     */
    private function limpiarDatos($post)
    {
        return [
            'afiliado_id' => !empty($post['afiliado_id']) ? (int) $post['afiliado_id'] : null,
            'nombre' => trim($post['nombre'] ?? ''),
            'descripcion' => trim($post['descripcion'] ?? ''),
            'departamento' => trim($post['departamento'] ?? ''),
            'oficina_id' => !empty($post['oficina_id']) ? (int) $post['oficina_id'] : null,
            'fecha_asignacion' => trim($post['fecha_asignacion'] ?? ''),
            'fecha_remocion' => trim($post['fecha_remocion'] ?? ''),
            'motivo_remocion' => trim($post['motivo_remocion'] ?? ''),
            'salario_base' => !empty($post['salario_base']) ? (float) $post['salario_base'] : null,
            'tipo_contrato' => $post['tipo_contrato'] ?? 'indefinido',
            'jornada' => $post['jornada'] ?? 'completa',
            'estado' => $post['estado'] ?? 'activo',
            'asignado_por' => !empty($post['asignado_por']) ? (int) $post['asignado_por'] : null,
            'observaciones' => trim($post['observaciones'] ?? ''),
        ];
    }
}
