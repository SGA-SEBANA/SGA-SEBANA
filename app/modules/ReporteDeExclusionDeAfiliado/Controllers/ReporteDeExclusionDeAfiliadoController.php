<?php
namespace App\Modules\ReporteDeExclusionDeAfiliado\Controllers;

use App\Core\ControllerBase;
use App\Modules\ReporteDeExclusionDeAfiliado\Models\ReporteDeExclusionDeAfiliado;
use Dompdf\Dompdf;

class ReporteDeExclusionDeAfiliadoController extends ControllerBase
{
    public function index()
    {
        $model = new ReporteDeExclusionDeAfiliado();
        $filtros = [
            'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
            'fecha_fin'    => $_GET['fecha_fin'] ?? null,
            'tipo_baja'    => $_GET['tipo_baja'] ?? null,
            'estado'       => $_GET['estado'] ?? null,
            'nombre'       => $_GET['nombre'] ?? null,
        ];

        $exclusiones = $model->getAll($filtros);

        return $this->render('index', [
            'exclusiones' => $exclusiones,
            'filtros' => $filtros
        ]);
    }

  public function show($id)
{
    $model = new ReporteDeExclusionDeAfiliado();
    $afiliado = $model->getById($id);

    if (!$afiliado) {
        return $this->render('error', ['mensaje' => 'Afiliado no encontrado']);
    }

    // Mostrar detalle aunque no tenga fecha_baja, siempre que esté inactivo o excluido
    if (empty($afiliado['fecha_baja']) && $afiliado['estado'] !== 'inactivo') {
        return $this->render('error', ['mensaje' => 'Afiliado no tiene exclusión registrada']);
    }

    return $this->render('show', ['afiliado' => $afiliado]);
}


   public function descargarPdf($id)
{
    $model = new ReporteDeExclusionDeAfiliado();
    $afiliado = $model->getById($id);

    if (!$afiliado) {
        echo "No se puede generar PDF. Afiliado no encontrado.";
        return;
    }

    // Permitir PDF si tiene fecha_baja o si está inactivo
    if (empty($afiliado['fecha_baja']) && $afiliado['estado'] !== 'inactivo') {
        echo "No se puede generar PDF. Afiliado sin baja registrada.";
        return;
    }

    $dompdf = new Dompdf();
    ob_start();
    include __DIR__ . '/../views/Plantilla_PDF_Exclusion.php';
    $html = ob_get_clean();
    $dompdf->loadHtml($html);
    $dompdf->render();
    $dompdf->stream('exclusion_'.$afiliado['cedula'].'.pdf', ['Attachment' => true]);
}
public function exportar($formato)
{
    $model = new ReporteDeExclusionDeAfiliado();
    $filtros = [
        'fecha_inicio' => $_GET['fecha_inicio'] ?? null,
        'fecha_fin'    => $_GET['fecha_fin'] ?? null,
        'tipo_baja'    => $_GET['tipo_baja'] ?? null,
        'estado'       => $_GET['estado'] ?? null,
    ];

    $exclusiones = $model->getAll($filtros);

    if ($formato === 'pdf') {
        $dompdf = new Dompdf();
        ob_start();
        include __DIR__ . '/../views/Plantilla_PDF_Exclusion_General.php';
        $html = ob_get_clean();
        $dompdf->loadHtml($html);
        $dompdf->render();
        $dompdf->stream('reporte_exclusiones.pdf', ['Attachment' => true]);
    
   } else {
        echo "Formato no soportado.";
    }
}



    /* ===============================
       Render de vistas (igual que Carné)
    ================================ */
    private function render($view, $data = [])
    {
        extract($data);
        $file = __DIR__ . '/../views/' . $view . '.php';

        if (file_exists($file)) {
            include $file;
        } else {
            echo "Vista no encontrada: $file";
        }
    }
}