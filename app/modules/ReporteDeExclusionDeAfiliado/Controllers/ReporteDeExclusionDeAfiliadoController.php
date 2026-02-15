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

        if (!$afiliado || empty($afiliado['fecha_baja'])) {
            return $this->render('error', ['mensaje' => 'Afiliado no tiene exclusión registrada']);
        }

        return $this->render('show', ['afiliado' => $afiliado]);
    }

    public function descargarPdf($id)
    {
        $model = new ReporteDeExclusionDeAfiliado();
        $afiliado = $model->getById($id);

        if (!$afiliado || empty($afiliado['fecha_baja'])) {
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