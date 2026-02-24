<?php

namespace App\Modules\Bitacora\Controllers;
use App\Modules\Bitacora\Models\BitacoraModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BitacoraController {



public function exportarExcel(){

    if (ob_get_length()) {
        ob_end_clean();
    }

    header_remove();

    $model = new BitacoraModel();
    $datos = $model->getBitacora();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $fila = 1;

    $sheet->fromArray([
        ['id','usuario_id',
        'accion','modulo',
        'entidad_id',
        'descripcion','
        datos_anteriores',
        'datos_nuevos',
        'ip_address','user_agent',
        'metodo_http','resultado',
        'codigo_error',
        'mensaje_error',
        'url_accedida',
        'duracion_ms']
        
    ], null, 'A1');

    $fila = 2;

    foreach ($datos as $row) {
        $sheet->fromArray(array_values($row), null, "A$fila");
        $fila++;
    }
    
    foreach(range('A', 'P') as $col){
        $spreadsheet->getActiveSheet()
        ->getColumnDimension($col)
        ->setWidth(120,'pt');
        
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
        'busqueda'  => trim($_GET['q'] ?? ''),
        'modulo'    => $_GET['modulo'] ?? '',
        'accion'    => $_GET['accion'] ?? '',
        'resultado' => $_GET['resultado'] ?? '',
        'fecha'     => $_GET['fecha'] ?? ''
    ];

    $model = new BitacoraModel();
    $bitacora = $model->getBitacora($filtros);


    $modulos    = $model->getModulos();
    $acciones   = $model->getAcciones();
    $resultados = $model->getResultados();


    require BASE_PATH . '/app/modules/Bitacora/Views/index.php';
}



public function detalles($id){
    $model = new BitacoraModel();
    $registro = $model->getBitacoraById($id);

    require BASE_PATH . '/app/modules/Bitacora/Views/detalles.php';
}


}