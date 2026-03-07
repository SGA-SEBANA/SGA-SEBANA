<?php

namespace App\Modules\CategoriasRRLL\Controllers;

use App\Core\ControllerBase;
use App\Modules\CategoriasRRLL\Models\CategoriasRRLLModel;
use App\Modules\Usuarios\Models\Bitacora; // para registrar acciones
use App\Modules\Bitacora\Models\BitacoraModel; // para consultar historial
use Dompdf\Dompdf;
use Dompdf\Options;

class CategoriasRRLLController extends ControllerBase {

    protected $model;

    public function __construct() {
        $this->model = new CategoriasRRLLModel();
    }

    // HU-CAT-04: Listado y filtros
    public function index() {
        $filtros = [
            'q' => $_GET['q'] ?? '',
            'fecha' => $_GET['fecha'] ?? ''
        ];
        $categorias = $this->model->obtenerTodas($filtros);
        $this->view('index', [
            'titulo' => 'Categorías de Casos RRLL',
            'categorias' => $categorias,
            'filtros' => $filtros
        ]);
    }

    // HU-CAT-01: Crear nueva categoría
    public function create() {
        $this->view('create', ['titulo' => 'Nueva Categoría RRLL']);
    }

    public function show($id) {
        $categoria = $this->model->find($id);

        if (!$categoria) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?error=no_encontrada');
            return;
        }

        $this->view('show', [
            'titulo' => 'Detalle Categoría RRLL',
            'categoria' => $categoria
        ]);
    }

    public function store() {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL/create?error=vacio');
            return;
        }

        if ($this->model->existeNombre($nombre)) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL/create?error=duplicado');
            return;
        }

        if ($this->model->registrar($nombre, $descripcion)) {
            // Bitácora HU-CAT-01
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'categorias_rrll',
                'entidad' => 'categoria',
                'descripcion' => "Creación de categoría RRLL: $nombre"
            ]);
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?success=creado');
        }
    }

    // HU-CAT-02: Editar
    public function edit($id) {
        $categoria = $this->model->find($id);
        $this->view('edit', ['categoria' => $categoria]);
    }

    public function update($id) {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($nombre)) {
            $this->redirect("/SGA-SEBANA/public/CategoriasRRLL/$id/edit?error=vacio");
            return;
        }

        if ($this->model->existeNombre($nombre, $id)) {
            $this->redirect("/SGA-SEBANA/public/CategoriasRRLL/$id/edit?error=duplicado");
            return;
        }

        if ($this->model->actualizar($id, $nombre, $descripcion)) {
            // Bitácora HU-CAT-02
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'categorias_rrll',
                'entidad' => 'categoria',
                'entidad_id' => $id,
                'descripcion' => "Actualización de categoría RRLL: $nombre"
            ]);
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?success=actualizado');
        }
    }

    // HU-CAT-03: Cambiar estado (activo ↔ inactivo)
    public function toggleEstado($id) {
        $categoria = $this->model->find($id);

        if (!$categoria) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?error=no_encontrada');
            return;
        }

        if ($categoria['estado'] === 'activo' && $this->model->tieneAsociaciones($id)) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?error=en_uso');
            return;
        }

        $nuevoEstado = $categoria['estado'] === 'activo' ? 'inactivo' : 'activo';

        if ($this->model->cambiarEstado($id, $nuevoEstado)) {
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'categorias_rrll',
                'entidad' => 'categoria',
                'entidad_id' => $id,
                'descripcion' => "Cambio de estado categoría RRLL ID: $id a $nuevoEstado"
            ]);
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?success=estado_actualizado');
        }
    }

    // HU-CAT-04: Exportar historial de modificaciones a PDF usando BitacoraModel
    public function exportarHistorialPDF() {
        $bitacoraModel = new BitacoraModel();
        $filtros = ['modulo' => 'categorias_rrll', 'accion' => 'UPDATE'];
        $datos = $bitacoraModel->getBitacora($filtros);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = '<h2 style="text-align:center;">Historial de Categorías RRLL Modificadas</h2>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">';
        $html .= '<thead><tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                  </tr></thead><tbody>';

        foreach ($datos as $row) {
            $html .= '<tr>
                        <td>'.htmlspecialchars($row['id']).'</td>
                        <td>'.htmlspecialchars($row['usuario_id']).'</td>
                        <td>'.htmlspecialchars($row['accion']).'</td>
                        <td>'.htmlspecialchars($row['descripcion']).'</td>
                        <td>'.htmlspecialchars($row['fecha_creacion'] ?? '').'</td>
                      </tr>';
        }

        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("historial_categorias_rrll.pdf", ["Attachment" => true]);
    }
}