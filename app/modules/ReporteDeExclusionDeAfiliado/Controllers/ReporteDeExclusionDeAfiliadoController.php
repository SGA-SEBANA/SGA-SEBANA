<?php
namespace App\Modules\ReporteDeExclusionDeAfiliado\Controllers;

use App\Core\ControllerBase;
use App\Modules\ReporteDeExclusionDeAfiliado\Models\ReporteDeExclusionDeAfiliado;
use Dompdf\Dompdf;
use App\Helpers\Paginator;

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

    $pagination = \App\Helpers\Paginator::make(
        $model,
        'getAll',
        $filtros,
        $_GET['page'] ?? 1,
        10
    );

    return $this->render('index', [
        'exclusiones' => $pagination['data'],
        'filtros' => $filtros,
        'page' => $pagination['page'],
        'totalPaginas' => $pagination['totalPaginas']
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
    include __DIR__ . '/../Views/Plantilla_PDF_Exclusion.php';
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

    if ($formato === 'pdf') {
        $exclusiones = $model->getAll($filtros, 0, 5000);
        $dompdf = new Dompdf();
        ob_start();
        include __DIR__ . '/../Views/Plantilla_PDF_Exclusion_General.php';
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
        $file = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($file)) {
            include $file;
        } else {
            echo "Vista no encontrada: $file";
        }
    }
}
