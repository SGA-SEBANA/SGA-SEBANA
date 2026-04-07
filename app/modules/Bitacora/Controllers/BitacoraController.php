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


    $sheet->getStyle('A1:N1')->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FFFFFFFF'],
            'size' => 12
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF1F4E78']
        ]
    ]);

    $fila = 2;

    foreach ($datos as $row) {

 
        $fecha = !empty($row['fecha_creacion'])
            ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($row['fecha_creacion']))
            : null;

        $sheet->setCellValue("A{$fila}", (string) ($row['id'] ?? ''));
        $sheet->setCellValue("B{$fila}", (string) ($row['usuario_id'] ?? ''));
        $sheet->setCellValue("C{$fila}", $fecha);
        $sheet->setCellValue("D{$fila}", (string) ($row['accion_label'] ?? $row['accion'] ?? ''));
        $sheet->setCellValue("E{$fila}", (string) ($row['modulo_label'] ?? $row['modulo'] ?? ''));
        $sheet->setCellValue("F{$fila}", (string) ($row['entidad_label'] ?? $row['entidad'] ?? ''));
        $sheet->setCellValue("G{$fila}", (string) ($row['entidad_id'] ?? ''));
        $sheet->setCellValue("H{$fila}", (string) ($row['descripcion'] ?? ''));
        $sheet->setCellValue("I{$fila}", (string) ($row['resultado_label'] ?? $row['resultado'] ?? ''));
        $sheet->setCellValue("J{$fila}", (string) ($row['ip_address'] ?? ''));
        $sheet->setCellValue("K{$fila}", (string) ($row['metodo_http'] ?? ''));
        $sheet->setCellValue("L{$fila}", (string) ($row['url_accedida'] ?? ''));
        $sheet->setCellValue("M{$fila}", (string) ($row['codigo_error'] ?? ''));
        $sheet->setCellValue("N{$fila}", (string) ($row['mensaje_error'] ?? ''));

        $fila++;
    }

  
    $sheet->getStyle("C2:C{$fila}")
        ->getNumberFormat()
        ->setFormatCode('dd/mm/yyyy hh:mm:ss');


    for ($i = 2; $i < $fila; $i++) {
        $resultado = $sheet->getCell("I{$i}")->getValue();

        if ($resultado === 'Exitoso') {
            $sheet->getStyle("I{$i}")->getFont()->getColor()->setARGB('FF008000');
        } elseif ($resultado === 'Fallido') {
            $sheet->getStyle("I{$i}")->getFont()->getColor()->setARGB('FFFF0000');
        }
    }

 
    for ($i = 2; $i < $fila; $i++) {
        if ($i % 2 == 0) {
            $sheet->getStyle("A{$i}:N{$i}")->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFF2F2F2');
        }
    }

    
    foreach (range('A', 'N') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

  
    $sheet->freezePane('A2');


    $sheet->setAutoFilter("A1:N1");

 
    $sheet->getStyle("A1:N" . ($fila - 1))->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
            ]
        ]
    ]);

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
