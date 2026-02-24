<?php
namespace App\Modules\Categorias\Controllers;

use App\Core\ControllerBase;
use App\Modules\Categorias\Models\CategoriaModel;

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

    /**
     * HU-GC-04 (Escenario 3): Muestra la información completa de una categoría
     */
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
                $tipo = trim($_POST['tipo'] ?? 'general'); // Ensure $tipo is passed correctly

            if (empty($nombre)) {
                $this->redirect('/SGA-SEBANA/public/Categorias/create?error=vacio');
                return;
            }

            if ($this->model->existeNombre($nombre)) {
                $this->redirect('/SGA-SEBANA/public/Categorias/create?error=duplicado');
                return;
            }

                if ($this->model->registrar($nombre, $descripcion, $tipo)) {
                // HU-GC-01: Notificación y correo de registro exitoso
                $this->enviarNotificacionMail($nombre, 'registro');
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
                $tipo = trim($_POST['tipo'] ?? 'general'); // Ensure $tipo is passed correctly

            if (empty($nombre)) {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=vacio");
                return;
            }

            if ($this->model->existeNombre($nombre, $id)) {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=duplicado");
                return;
            }

                if ($this->model->actualizar($id, $nombre, $descripcion, $tipo)) {
                // HU-GC-02: Notificación y correo informando la actualización
                $this->enviarNotificacionMail($nombre, 'actualización');
                $this->redirect('/SGA-SEBANA/public/Categorias?success=actualizado');
            } else {
                $this->redirect("/SGA-SEBANA/public/Categorias/$id/edit?error=db");
            }
        }
    }

    public function toggle($id) {
        $categoria = $this->model->find($id);
        if ($this->model->toggleStatus($id)) {
            // HU-GC-03: Notificación y correo confirmando la acción
            $this->enviarNotificacionMail($categoria['nombre'] ?? 'ID: '.$id, 'cambio de estado');
            $this->redirect('/SGA-SEBANA/public/Categorias?success=estado_cambiado');
        } else {
            $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
        }
    }

    /**
     * HU-GC-03: Gestión inteligente de eliminación e inactivación
     */
    public function delete($id) {
        $categoria = $this->model->find($id);
        $nombre = $categoria['nombre'] ?? 'ID: '.$id;

        if ($this->model->tieneAsociaciones($id)) {
            if ($this->model->inactivar($id)) {
                // HU-GC-03: Notificación y correo tras inactivación lógica
                $this->enviarNotificacionMail($nombre, 'inactivación (con elementos asociados)');
                $this->redirect('/SGA-SEBANA/public/Categorias?success=inactivado_por_asociacion');
            } else {
                $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
            }
            return;
        }

        if ($this->model->eliminar($id)) {
            // HU-GC-03: Notificación y correo tras eliminación física
            $this->enviarNotificacionMail($nombre, 'eliminación física');
            $this->redirect('/SGA-SEBANA/public/Categorias?success=eliminado_fisico');
        } else {
            $this->redirect('/SGA-SEBANA/public/Categorias?error=db');
        }
    }

    /**
     * Centraliza las notificaciones de correo para auditoría
     */
    private function enviarNotificacionMail($nombreCategoria, $accion) {
        $mensaje = "SGA-SEBANA: Se ha procesado con éxito la " . $accion . " de la categoría: " . $nombreCategoria;
        error_log("SIMULACIÓN DE CORREO: " . $mensaje);
    }
}