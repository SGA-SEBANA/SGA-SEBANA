<?php
namespace App\Modules\Afiliados\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados; 

class AfiliadosController extends ControllerBase {

    public function index() {
        $modelo = new Afiliados();
        $afiliados = $modelo->getAll();

        $data = [
            'titulo' => 'Gestión de Afiliados',
            'afiliados' => $afiliados,
            'success' => $_GET['success'] ?? null 
        ];

        $this->view('index', $data);
    }

    public function create() {
        $data = [
            'titulo' => 'Registrar Nuevo Afiliado',
            'success' => isset($_GET['status']) && $_GET['status'] === 'success' ? "¡Afiliado registrado correctamente!" : null, 
            'error'   => null
        ];

        $this->view('create', $data); 
    }

    public function store() {
        $datos = $this->limpiarDatos($_POST);
        $modelo = new Afiliados();

        if ($modelo->existeCedula($datos['cedula'])) {
            echo "<script>alert('Error: Cédula duplicada.'); window.history.back();</script>";
            return;
        }

        if ($modelo->create($datos)) {
            header('Location: /SGA-SEBANA/public/afiliados/create?status=success');
            exit;
        } else {
            echo "Error al guardar.";
        }
    }

    public function edit($id) {
        $modelo = new Afiliados();
        $afiliado = $modelo->getById($id);

        if (!$afiliado) {
            echo "Afiliado no encontrado.";
            return;
        }

        $data = [
            'titulo' => 'Editar Afiliado',
            'afiliado' => $afiliado
        ];

        $this->view('edit', $data);
    }

    public function update($id) {
        $datos = $this->limpiarDatos($_POST);
        $modelo = new Afiliados();

        if ($modelo->existeCedula($datos['cedula'], $id)) {
            echo "<script>alert('Error: Esa cédula ya pertenece a otro afiliado.'); window.history.back();</script>";
            return;
        }

        if ($modelo->update($id, $datos)) {
            header('Location: /SGA-SEBANA/public/afiliados?success=Afiliado actualizado correctamente');
            exit;
        } else {
            echo "Error al actualizar.";
        }
    }

    /**
     * NUEVO (HU-AF-04): Cambiar estado (Activar/Desactivar)
     */
    public function toggle($id) {
        $modelo = new Afiliados();
        
        if ($modelo->toggleStatus($id)) {
            header('Location: /SGA-SEBANA/public/afiliados?success=Estado actualizado correctamente');
            exit;
        } else {
            echo "Error al cambiar el estado.";
        }
    }

    private function limpiarDatos($post) {
        return [
            'nombre_completo'     => trim($post['nombre_completo'] ?? ''),
            'cedula'              => trim($post['cedula'] ?? ''),
            'numero_empleado'     => trim($post['numero_empleado'] ?? ''),
            'genero'              => trim($post['genero'] ?? ''),
            'fecha_nacimiento'    => trim($post['fecha_nacimiento'] ?? ''),
            'oficina_nombre'      => trim($post['oficina_nombre'] ?? ''),
            'oficina_numero'      => trim($post['oficina_numero'] ?? ''),
            'categoria'           => trim($post['categoria'] ?? ''),
            'email_institucional' => trim($post['email_institucional'] ?? ''),
            'celular_personal'    => trim($post['celular_personal'] ?? '')
        ];
    }
}