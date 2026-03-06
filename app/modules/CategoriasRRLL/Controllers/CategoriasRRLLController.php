<?php

namespace App\Modules\CategoriasRRLL\Controllers;

use App\Core\ControllerBase;
use App\Modules\CategoriasRRLL\Models\CategoriasRRLLModel;
use App\Modules\Usuarios\Models\Bitacora;

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

    // HU-CAT-03: Eliminar
    public function delete($id) {
        if ($this->model->tieneAsociaciones($id)) {
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?error=en_uso');
            return;
        }
        if ($this->model->eliminar($id)) {
            // Bitácora HU-CAT-03
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'DELETE',
                'modulo' => 'categorias_rrll',
                'entidad' => 'categoria',
                'entidad_id' => $id,
                'descripcion' => "Eliminación de categoría RRLL ID: $id"
            ]);
            $this->redirect('/SGA-SEBANA/public/CategoriasRRLL?success=eliminado');
        }
    }
}