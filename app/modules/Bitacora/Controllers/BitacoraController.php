<?php

namespace App\Modules\Bitacora\Controllers;

use App\Modules\Bitacora\Models\BitacoraModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BitacoraController
{
    public function exportarExcel()
    {
        if (ob_get_length()) {
            ob_end_clean();
        }

        header_remove();

        $filtros = [
            'busqueda' => trim((string) ($_GET['q'] ?? '')),
            'modulo' => (string) ($_GET['modulo'] ?? ''),
            'accion' => (string) ($_GET['accion'] ?? ''),
            'resultado' => (string) ($_GET['resultado'] ?? ''),
            'fecha' => (string) ($_GET['fecha'] ?? '')
        ];

        $model = new BitacoraModel();
        $datos = $model->getBitacora($filtros, 0, null);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray([
            [
                'ID',
                'Usuario ID',
                'Fecha',
                'Accion',
                'Modulo',
                'Entidad',
                'Entidad ID',
                'Descripcion',
                'Resultado',
                'IP',
                'Metodo HTTP',
                'URL',
                'Codigo error',
                'Mensaje error'
            ]
        ], null, 'A1');

        $fila = 2;
        foreach ($datos as $row) {
            $sheet->fromArray([
                (string) ($row['id'] ?? ''),
                (string) ($row['usuario_id'] ?? ''),
                (string) ($row['fecha_creacion'] ?? ''),
                (string) ($row['accion_label'] ?? $row['accion'] ?? ''),
                (string) ($row['modulo_label'] ?? $row['modulo'] ?? ''),
                (string) ($row['entidad_label'] ?? $row['entidad'] ?? ''),
                (string) ($row['entidad_id'] ?? ''),
                (string) ($row['descripcion'] ?? ''),
                (string) ($row['resultado_label'] ?? $row['resultado'] ?? ''),
                (string) ($row['ip_address'] ?? ''),
                (string) ($row['metodo_http'] ?? ''),
                (string) ($row['url_accedida'] ?? ''),
                (string) ($row['codigo_error'] ?? ''),
                (string) ($row['mensaje_error'] ?? '')
            ], null, "A{$fila}");
            $fila++;
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="bitacora.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function index()
    {
        $filtros = [
            'busqueda' => trim((string) ($_GET['q'] ?? '')),
            'modulo' => (string) ($_GET['modulo'] ?? ''),
            'accion' => (string) ($_GET['accion'] ?? ''),
            'resultado' => (string) ($_GET['resultado'] ?? ''),
            'fecha' => (string) ($_GET['fecha'] ?? '')
        ];

        $model = new BitacoraModel();

        $limit = 15;
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $page = max($page, 1);
        $start = ($page - 1) * $limit;

        $bitacora = $model->getBitacora($filtros, $start, $limit);
        $totalRegistros = $model->countBitacora($filtros);
        $totalPaginas = max((int) ceil($totalRegistros / $limit), 1);

        $modulos = $model->getModulos();
        $acciones = $model->getAcciones();
        $resultados = $model->getResultados();

        require BASE_PATH . '/app/modules/Bitacora/Views/index.php';
    }

    public function detalles($id)
    {
        $model = new BitacoraModel();
        $registro = $model->getBitacoraById($id);

        require BASE_PATH . '/app/modules/Bitacora/Views/detalles.php';
    }
}
