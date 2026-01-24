<?php

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





      // HU-CR-02: Criterio 1 - Generar y descargar PDF
    public function descargarPdf($afiliadoId) {
        $afiliado = Afiliado::find($afiliadoId);

        if ($afiliado && $afiliado->estado === 'activo') {
            $dompdf = new Dompdf();

            // Cargar plantilla base
            $html = file_get_contents('templates/carne.html.php');

            // Reemplazar variables dinámicas
            $html = str_replace('{{nombre}}', $afiliado->nombre, $html);
            $html = str_replace('{{cedula}}', $afiliado->cedula, $html);
            $html = str_replace('{{estado}}', $afiliado->estado, $html);
            $html = str_replace('{{fecha}}', date('d/m/Y'), $html);
            $html = str_replace('{{foto}}', $afiliado->foto, $html);

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
?>