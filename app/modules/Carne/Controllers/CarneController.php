<?php
namespace App\Modules\Carne\Controllers;

use App\Core\ControllerBase;
use Dompdf\Dompdf;
use Endroid\QrCode\Builder\Builder;

class CarneController extends ControllerBase
{

    /* ===============================
       Vista previa del carné
    ================================ */
    public function generar($afiliadoId)
    {

        $afiliadoModel = new \App\Modules\Afiliados\Models\Afiliados();
        $afiliado = $afiliadoModel->getById($afiliadoId);

        if ($afiliado && !empty($afiliado['nombre']) && !empty($afiliado['cedula'])) {

            if ($afiliado['estado'] === 'activo') {
                return $this->render('preview', [
                    'afiliado' => $afiliado
                ]);
            }

            return $this->render('error', [
                'mensaje' => 'El afiliado está inactivo. No se puede generar el carné.'
            ]);
        }

        $faltantes = [];
        if (empty($afiliado['nombre']))
            $faltantes[] = 'Nombre';
        if (empty($afiliado['cedula']))
            $faltantes[] = 'Cédula';

        return $this->render('error', [
            'mensaje' => 'Complete los campos: ' . implode(', ', $faltantes)
        ]);
    }

    /* ===============================
       Emitir carné con QR dinámico
    ================================ */
    public function emitir($id)
    {

        $afiliadoModel = new \App\Modules\Afiliados\Models\Afiliados();
        $afiliado = $afiliadoModel->getById($id);

        if (!$afiliado || $afiliado['estado'] !== 'activo') {
            return $this->render('error', [
                'mensaje' => 'El afiliado está inactivo. No se puede emitir el carné.'
            ]);
        }

        $carnetModel = new \App\Modules\Carne\Models\Carne();
        $carnet = $carnetModel->getByAfiliadoId($id);

        $nuevoQr = $this->generarNuevoQr($id);

        if ($carnet) {
            $carnetModel->updateQr($id, $nuevoQr);
        } else {
            $carnetModel->create([
                'afiliado_id' => $id,
                'qr_code' => $nuevoQr,
                'version' => 1,
                'estado' => 'activo'
            ]);
        }

        $qr_image = $this->generarQrBase64($nuevoQr);

        return $this->render('show', [
            'afiliado' => $afiliado,
            'qr_code' => $nuevoQr,
            'qr_image' => $qr_image,
            'mensaje' => 'Carné emitido correctamente.'
        ]);
    }

    /* ===============================
       Descargar PDF del carné
    ================================ */
    public function descargarPdf($afiliadoId)
    {

        $afiliadoModel = new \App\Modules\Afiliados\Models\Afiliados();
        $afiliado = $afiliadoModel->getById($afiliadoId);

        if (!$afiliado || $afiliado['estado'] !== 'activo') {
            echo "No se puede generar el PDF. Afiliado inactivo.";
            return;
        }

        /* ===== QR BASE64 ===== */
        $qr_image = $this->generarQrBase64($afiliado['cedula']);

        /* ===== LOGO BASE64 ===== */
        $logoPath = BASE_PATH . '/public/assets/img/icon/sebana_logo-removebg.png';

        if (!file_exists($logoPath)) {
            // Fallback to jpg if png is missing
            $logoPath = BASE_PATH . '/public/assets/img/icon/sebana_logo.jpg';
        }

        if (!file_exists($logoPath)) {
            echo "No se encontró el logo en: " . $logoPath;
            return;
        }

        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
        $logo_image = 'data:image/' . $type . ';base64,' . base64_encode(
            file_get_contents($logoPath)
        );

        /* ===== DOMPDF ===== */
        $dompdf = new Dompdf(['isRemoteEnabled' => true]);

        ob_start();
        include $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/carne.html.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A6', 'portrait');
        $dompdf->render();

        $dompdf->stream(
            'carne_' . $afiliado['cedula'] . '.pdf',
            ['Attachment' => true]
        );
    }

    /* ===============================
       Generar nuevo QR
    ================================ */
    private function generarNuevoQr($afiliadoId)
    {
        return hash('sha256', $afiliadoId . time());
    }

    /* ===============================
       QR en Base64
    ================================ */
    private function generarQrBase64($data)
    {

        $result = Builder::create()
            ->data($data)
            ->size(200) // Reduced size for performance
            ->margin(5)  // Smaller margin
            ->build();

        return 'data:image/png;base64,' . base64_encode($result->getString());
    }

    /* ===============================
       Render de vistas
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
