<?php

namespace App\Modules\Categorias\Controllers;

use App\Core\ControllerBase;
use App\Modules\Categorias\Models\CategoriaModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Modules\Bitacora\Models\BitacoraModel;
use App\Modules\Usuarios\Models\Bitacora; // <-- agregado para registrar acciones

class CategoriaController extends ControllerBase {
    
    protected $model;

    public function __construct() {
        $this->model = new CategoriaModel();
    }

    public function index() {
        $filtros = [
            'q' => $_GET['q'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'tipo' => $_GET['tipo'] ?? ''
        ];

        $categorias = $this->model->obtenerTodas($filtros);

        $this->view('index', [
            'titulo' => 'Listado de Categorías',
            'categorias' => $categorias,
            'filtros' => $filtros,
            'success' => $_GET['success'] ?? null,
            'error' => $_GET['error'] ?? null
        ]);
    }

    public function show($id) {
        $categoria = $this->model->find($id);
        if (!$categoria) {
            $this->redirect('/SGA-SEBANA/public/Categorias?error=no_encontrado');
            return;
        }
        $this->view('show', [
            'titulo' => 'Detalles de la Categoría',
            'categoria' => $categoria
        ]);
    }

    public function create() {
        $this->view('create', [
            'titulo' => 'Registrar Nueva Categoría'
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo = trim($_POST['tipo'] ?? 'general');

            if (empty($nombre)) {
                $this->redirect('/SGA-SEBANA/public/Categorias/create?error=vacio');
                return;
            }

            if ($this->model->existeNombre($nombre)) {
                $this->redirect('/SGA-SEBANA/public/Categorias/create?error=duplicado');
                return;
            }

            if ($this->model->registrar($nombre, $descripcion, $tipo)) {
                $this->enviarNotificacionMail($nombre, 'registro');

                // BITÁCORA: creación
                $bitacora = new Bitacora();
                $bitacora->log([
                    'accion' => 'CREATE',
                    'modulo' => 'categorias',
                    'entidad' => 'categoria',
                    'descripcion' => "Creación de categoría: $nombre"
                ]);

                $this->redirect('/SGA-SEBANA/public/Categorias?success=creado');
                return;
            } else {
                $this->redirect('/SGA-SEBANA/public/Categorias/create?error=db');
                return;
            }
        }
    }

    public function edit($id) {
        $categoria = $this->model->find($id);
        if (!$categoria) {
            $this->redirect('/SGA-SEBANA/public/Categorias?error=no_encontrado');
            return;
        }
        $this->view('edit', [
            'titulo' => 'Editar Categoría',
            'categoria' => $categoria
        ]);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $tipo = trim($_POST['tipo'] ?? 'general');

            if (empty($nombre)) {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=vacio");
                return;
            }

            if ($this->model->existeNombre($nombre, $id)) {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=duplicado");
                return;
            }

            if ($this->model->actualizar($id, $nombre, $descripcion, $tipo)) {
                $this->enviarNotificacionMail($nombre, 'actualización');

                // BITÁCORA: actualización
                $bitacora = new Bitacora();
                $bitacora->log([
                    'accion' => 'UPDATE',
                    'modulo' => 'categorias',
                    'entidad' => 'categoria',
                    'entidad_id' => $id,
                    'descripcion' => "Actualización de categoría: $nombre"
                ]);

                $this->redirect('/SGA-SEBANA/public/Categorias?success=actualizado');
            } else {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=db");
            }
        }
    }

    public function toggle($id) {
    $categoria = $this->model->find($id);

    if (!$categoria) {
        $this->redirect('/SGA-SEBANA/public/Categorias?error=no_encontrado');
        return;
    }

    // Bloquear si está activa y en uso
    if ($categoria['estado'] === 'activo' && $this->model->tieneAsociaciones($id)) {
        $this->redirect('/SGA-SEBANA/public/Categorias?error=en_uso');
        return;
    }

    if ($this->model->toggleStatus($id)) {
        $this->enviarNotificacionMail($categoria['nombre'] ?? 'ID: '.$id, 'cambio de estado');

        // BITÁCORA: cambio de estado
        $bitacora = new Bitacora();
        $bitacora->log([
            'accion' => 'UPDATE',
            'modulo' => 'categorias',
            'entidad' => 'categoria',
            'entidad_id' => $id,
            'descripcion' => "Cambio de estado categoría ID: $id"
        ]);

        $this->redirect('/SGA-SEBANA/public/Categorias?success=estado_cambiado');
    } else {
        $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
    }
 }

    public function delete($id) {
        $categoria = $this->model->find($id);
        $nombre = $categoria['nombre'] ?? 'ID: '.$id;

        if ($this->model->tieneAsociaciones($id)) {
            if ($this->model->inactivar($id)) {
                $this->enviarNotificacionMail($nombre, 'inactivación (con elementos asociados)');

                // BITÁCORA: inactivación
                $bitacora = new Bitacora();
                $bitacora->log([
                    'accion' => 'UPDATE',
                    'modulo' => 'categorias',
                    'entidad' => 'categoria',
                    'entidad_id' => $id,
                    'descripcion' => "Inactivación de categoría (con asociaciones): $nombre"
                ]);

                $this->redirect('/SGA-SEBANA/public/Categorias?success=inactivado_por_asociacion');
            } else {
                $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
            }
            return;
        }

        if ($this->model->eliminar($id)) {
            $this->enviarNotificacionMail($nombre, 'eliminación física');

            // BITÁCORA: eliminación
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'DELETE',
                'modulo' => 'categorias',
                'entidad' => 'categoria',
                'entidad_id' => $id,
                'descripcion' => "Eliminación física de categoría: $nombre"
            ]);

            $this->redirect('/SGA-SEBANA/public/Categorias?success=eliminado_fisico');
        } else {
            $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
        }
    }

    private function enviarNotificacionMail($nombreCategoria, $accion) {
        $mensaje = "SGA-SEBANA: Se ha procesado con éxito la " . $accion . " de la categoría: " . $nombreCategoria;
        error_log("SIMULACIÓN DE CORREO: " . $mensaje);
    }

        public function exportarHistorialPDF() {
        $bitacoraModel = new BitacoraModel();
        $filtros = ['modulo' => 'categorias']; // ahora sí habrá registros
        $datos = $bitacoraModel->getBitacora($filtros);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = '<h2 style="text-align:center;">Historial de Categorías</h2>';
        $html .= '<table border="1" cellspacing="0" cellpadding="5" width="100%">';
        $html .= '<thead><tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                  </tr></thead><tbody>';

        if (!empty($datos)) {
            foreach ($datos as $row) {
                $accion = $row['accion'];
            $accionTraducida = [
                'CREATE' => 'Creación',
                'UPDATE' => 'Actualización',
                'DELETE' => 'Eliminación'
            ][$accion] ?? $accion;

                $html .= '<tr>
                            <td>'.htmlspecialchars($row['id']).'</td>
                            <td>'.htmlspecialchars($row['usuario_id']).'</td>
                            <td>'.htmlspecialchars($accionTraducida).'</td>

                            <td>'.htmlspecialchars($row['descripcion']).'</td>
                            <td>'.htmlspecialchars($row['fecha_creacion'] ?? '').'</td>
                          </tr>';
            }
        } else {
            $html .= '<tr><td colspan="5" style="text-align:center;">No hay registros en la bitácora</td></tr>';
        }

        $html .= '</tbody></table>';

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("historial_categorias.pdf", ["Attachment" => true]);
    }
}