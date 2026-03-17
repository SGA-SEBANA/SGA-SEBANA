<?php
namespace App\Modules\oficinas\Controllers;

use App\Modules\oficinas\Models\OfficeModel;
use App\Modules\Usuarios\Models\Bitacora;

class OfficeController {

    protected $officeModel;

    public function __construct() {
        $this->officeModel = new OfficeModel();
    }


    public function index() {
        $offices = $this->officeModel->getOffice();
        require BASE_PATH . '/app/modules/oficinas/view/index.php';
    }
    
    // Crear oficina
    public function create() {
        session_start();

        if ($_POST) {
            $officeId = $this->officeModel->createOffice(
                $_POST['codigo'],
                $_POST['nombre'],
                $_POST['direccion'],
                $_POST['provincia'],
                $_POST['canton'],
                $_POST['distrito'],
                $_POST['telefono'],
                $_POST['correo'],
                $_POST['horario_atencion'],
                $_POST['responsable'],
                1, // activo por defecto
                $_POST['coordenadas_gps'],
                $_POST['observaciones']
            );

            // Log Bitacora
            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'oficinas',
                'entidad' => 'oficina',
                'entidad_id' => $officeId,
                'descripcion' => "Creación de oficina: {$_POST['nombre']}",
                'datos_nuevos' => $_POST
            ]);

            header("Location: /oficinas");
            exit;
        }

        require BASE_PATH . '/app/modules/oficinas/View/create.php';
    }


    public function edit($id) {
        session_start();

        if ($_POST) {
            $this->officeModel->editOffice(
                $id,
                $_POST['codigo'],
                $_POST['nombre'],
                $_POST['direccion'],
                $_POST['provincia'],
                $_POST['canton'],
                $_POST['distrito'],
                $_POST['telefono'],
                $_POST['correo'],
                $_POST['horario_atencion'],
                $_POST['responsable'],
                $_POST['activo'],
                $_POST['coordenadas_gps'],
                $_POST['observaciones']
            );


            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'oficinas',
                'entidad' => 'oficina',
                'entidad_id' => $id,
                'descripcion' => "Actualización de oficina ID: {$id}",
                'datos_nuevos' => $_POST
            ]);

            header("Location: /oficinas");
            exit;
        }


        $office = $this->officeModel->getOfficeById($id);
        require BASE_PATH . '/app/modules/oficinas/View/edit.php';
    }


    public function delete($id) {
        $this->officeModel->deleteOffice($id);


        $bitacora = new Bitacora();
        $bitacora->log([
            'accion' => 'DELETE',
            'modulo' => 'oficinas',
            'entidad' => 'oficina',
            'entidad_id' => $id,
            'descripcion' => "Eliminación de oficina ID: {$id}"
        ]);

        header("Location: /oficinas");
        exit;
    }
}