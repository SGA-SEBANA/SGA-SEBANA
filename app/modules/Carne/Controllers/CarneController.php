<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Dompdf\Dompdf;

class CarneController extends ControllerBase {


// Criterios 1 ,2 y 3 modulo carnet de (primera historia):

    // Criterios 1 y 2: Generar vista previa del carné
    public function generar($afiliadoId) {
        $afiliado = Afiliado::find($afiliadoId);

        // Validar datos obligatorios
        if ($afiliado && $afiliado->nombre && $afiliado->cedula) {
            // Validar estado activo
            if ($afiliado->estado === 'activo') {
                // Criterio 1: Generar vista previa
                return $this->render('carne/preview', [
                    'afiliado' => $afiliado
                ]);
            } else {
                // Criterio 3: Estado inactivo
                return $this->render('carne/error', [
                    'mensaje' => 'El afiliado está inactivo. No se puede generar el carné.'
                ]);
            }
        } else {
            // Criterio 2: Datos incompletos
            $faltantes = [];
            if (!$afiliado->nombre) $faltantes[] = "Nombre";
            if (!$afiliado->cedula) $faltantes[] = "Cédula";

            return $this->render('carne/error', [
                'mensaje' => 'No se puede generar el carné. Complete los campos: ' . implode(", ", $faltantes)
            ]);
        }
    }

    // Criterio 3: Confirmar emisión del carné
    public function emitir($afiliadoId) {
        $afiliado = Afiliado::find($afiliadoId);

        if ($afiliado && $afiliado->estado === 'activo') {
            return $this->render('carne/show', [
                'afiliado' => $afiliado,
                'mensaje' => 'Carné emitido correctamente.'
            ]);
        } else {
            return $this->render('carne/error', [
                'mensaje' => 'El afiliado está inactivo. No se puede emitir el carné.'
            ]);
        }
    }





      // HU-CR-02: Criterio 1 - Generar y descargar PDF - ademas la plantilla , con la plantilla ya cumple en 2 y 3 criterio 
public function descargarPdf($afiliadoId) {
    $afiliado = Afiliado::find($afiliadoId);

    if ($afiliado && $afiliado->estado === 'activo') {
        $dompdf = new Dompdf();

        // Renderizar plantilla con datos dinámicos
        ob_start();
        
        
        include __DIR__ . '/../templates/carne.html.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A6', 'portrait'); // tamaño tipo carné
        $dompdf->render();

        // Descargar PDF
        $dompdf->stream("carne_".$afiliado->cedula.".pdf", ["Attachment" => true]);
    } else {
        return $this->render('carne/error', [
            'mensaje' => 'El afiliado está inactivo. No se puede descargar el carné.'
        ]);
    }
}


    }
     // Historia de usuario 3 , criterio 2 y3 
    public function validarQr($afiliadoId) {
    $afiliado = Afiliado::find($afiliadoId);

    if ($afiliado) {
        if ($afiliado->estado === 'inactivo') {
            return $this->render('carne/error', [
                'mensaje' => 'Afiliado inactivo. No válido para uso.'
            ]);
        }

        return $this->render('carne/validar', [
            'nombre' => $afiliado->nombre,
            'cedula' => $afiliado->cedula,
            'estado' => $afiliado->estado
        ]);
    } else {
        return $this->render('carne/error', [
            'mensaje' => 'Afiliado no encontrado.'
        ]);
    }
}





?>