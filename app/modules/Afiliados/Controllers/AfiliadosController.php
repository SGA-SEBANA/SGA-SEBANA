<?php
namespace App\Modules\Afiliados\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\Usuarios\Models\Bitacora;

class AfiliadosController extends ControllerBase
{

    public function index()
    {
        // 1. Capturar filtros de la URL (GET)
        $filtros = [
            'busqueda' => trim($_GET['q'] ?? ''),
            'estado' => $_GET['estado'] ?? '',
            'oficina_id' => $_GET['oficina_id'] ?? ''
        ];

        // 2. Consultar modelo con filtros
        $modelo = new Afiliados();
        $afiliados = $modelo->getAll($filtros);
        $oficinas = $modelo->getOficinas(); // Para el filtro

        // 3. Pasar datos a la vista
        $data = [
            'titulo' => 'Gestión de Afiliados',
            'afiliados' => $afiliados,
            'oficinas' => $oficinas,
            'filtros' => $filtros,
            'success' => $_GET['success'] ?? null
        ];

        $this->view('index', $data);
    }

    public function create()
    {
        $modelo = new Afiliados();

        $data = [
            'titulo' => 'Registrar Nuevo Afiliado',
            'categorias' => $modelo->getCategorias(),
            'oficinas' => $modelo->getOficinas(),
            'success' => isset($_GET['status']) && $_GET['status'] === 'success' ? "¡Afiliado registrado correctamente!" : null,
            'error' => null
        ];

        $this->view('create', $data);
    }

    public function store()
    {
        $datos = $this->limpiarDatos($_POST);
        $modelo = new Afiliados();

        if ($modelo->existeCedula($datos['cedula'])) {
            echo "<script>alert('Error: Cédula duplicada.'); window.history.back();</script>";
            return;
        }

        if ($modelo->create($datos)) {
            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'afiliados',
                'entidad' => 'afiliado',
                'descripcion' => "Creación de afiliado cédula: {$datos['cedula']}",
                'datos_nuevos' => $datos
            ]);

            header('Location: /SGA-SEBANA/public/afiliados/create?status=success');
            exit;
        } else {
            echo "Error al guardar.";
        }
    }

    public function edit($id)
    {
        $modelo = new Afiliados();
        $afiliado = $modelo->getById($id);

        if (!$afiliado) {
            echo "Afiliado no encontrado.";
            return;
        }

        $data = [
            'titulo' => 'Editar Afiliado',
            'afiliado' => $afiliado,
            'categorias' => $modelo->getCategorias(),
            'oficinas' => $modelo->getOficinas()
        ];

        $this->view('edit', $data);
    }

    public function update($id)
    {
        $datos = $this->limpiarDatos($_POST);
        $modelo = new Afiliados();

        // Obtener datos anteriores para log
        $anterior = $modelo->getById($id);

        if ($modelo->existeCedula($datos['cedula'], $id)) {
            echo "<script>alert('Error: Esa cédula ya pertenece a otro afiliado.'); window.history.back();</script>";
            return;
        }

        if ($modelo->update($id, $datos)) {
            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'afiliados',
                'entidad' => 'afiliado',
                'entidad_id' => $id,
                'descripcion' => "Actualización de afiliado ID: {$id}",
                'datos_anteriores' => $anterior,
                'datos_nuevos' => $datos
            ]);

            header('Location: /SGA-SEBANA/public/afiliados?success=Afiliado actualizado correctamente');
            exit;
        } else {
            echo "Error al actualizar.";
        }
    }

    public function toggle($id)
    {
        $modelo = new Afiliados();

        $nuevoEstado = $modelo->toggleStatus($id);

        if ($nuevoEstado) {
            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'STATUS_CHANGE',
                'modulo' => 'afiliados',
                'entidad' => 'afiliado',
                'entidad_id' => $id,
                'descripcion' => "Cambio de estado a: {$nuevoEstado}",
                'resultado' => 'exitoso'
            ]);

            header('Location: /SGA-SEBANA/public/afiliados?success=Estado actualizado correctamente');
            exit;
        } else {
            echo "Error al cambiar el estado.";
        }
    }

    private function limpiarDatos($post)
    {
        // Empaquetar datos de contacto de emergencia en JSON
        $contactoEmergencia = [
            'nombre' => trim($post['emergencia_nombre'] ?? ''),
            'telefono' => trim($post['emergencia_telefono'] ?? ''),
            'relacion' => trim($post['emergencia_relacion'] ?? '')
        ];

        return [
            // Básicos
            'nombre' => trim($post['nombre'] ?? ''),
            'apellido1' => trim($post['apellido1'] ?? ''),
            'apellido2' => trim($post['apellido2'] ?? ''),
            'cedula' => trim($post['cedula'] ?? ''),
            'genero' => trim($post['genero'] ?? ''),
            'fecha_nacimiento' => trim($post['fecha_nacimiento'] ?? ''),

            // Contacto
            'correo' => trim($post['correo'] ?? ''),
            'telefono' => trim($post['telefono'] ?? ''),
            'telefono_secundario' => trim($post['telefono_secundario'] ?? ''),
            'direccion' => trim($post['direccion'] ?? ''),

            // Laboral / Ubicación
            'categoria_id' => !empty($post['categoria_id']) ? (int) $post['categoria_id'] : null,
            'oficina_id' => !empty($post['oficina_id']) ? (int) $post['oficina_id'] : null,
            'puesto_actual' => trim($post['puesto_actual'] ?? ''),

            // Extras
            'datos_contacto_emergencia' => json_encode($contactoEmergencia), // Guardamos como JSON string
            'observaciones' => trim($post['observaciones'] ?? '')
        ];
    }
  public function desactivar($id)
{
    $modelo = new Afiliados();
    $afiliado = $modelo->getById($id);

    if (!$afiliado) {
        header('Location: /SGA-SEBANA/public/afiliados');
        exit;
    }

    $data = [
        'titulo' => 'Desactivar Afiliado',
        'afiliado' => $afiliado
    ];

    $this->view('baja', $data);
}

    public function procesarBaja($id)
{
    $modelo = new Afiliados();

    $data = [
        'fecha_baja' => date('Y-m-d'), 
        'motivo_baja' => $_POST['motivo_baja'],
        'tipo_baja' => $_POST['tipo_baja']
    ];

    $modelo->registrarBaja($id, $data);

    header('Location: /SGA-SEBANA/public/afiliados?success=Afiliado desactivado correctamente');
    exit;
}




}
