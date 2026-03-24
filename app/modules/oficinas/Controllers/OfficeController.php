<?php

namespace App\Modules\Oficinas\Controllers;

use App\Modules\Oficinas\Models\OfficeModel;
use App\Modules\Usuarios\Helpers\SecurityHelper;

class OfficeController
{
    private OfficeModel $officeModel;

    public function __construct()
    {
        $this->officeModel = new OfficeModel();
    }

    public function index()
    {
        SecurityHelper::requireAuth();
        $offices = $this->officeModel->getAll();
        $authUser = SecurityHelper::getAuthUser();
        require BASE_PATH . '/app/modules/Oficinas/View/index.php';
    }

    public function create()
{
    SecurityHelper::requireAuth();
    $authUser = SecurityHelper::getAuthUser();

    if ($_POST) {
        $data = [
            'codigo' => trim($_POST['codigo'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
            'canton' => trim($_POST['canton'] ?? ''),
            'distrito' => trim($_POST['distrito'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'horario_atencion' => trim($_POST['horario_atencion'] ?? ''),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'coordenadas_gps' => trim($_POST['coordenadas_gps'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        $this->officeModel->createOffice($data);
        header("Location: /SGA-SEBANA/public/oficinas");
        exit;
    }

    $office = [];
    require BASE_PATH . '/app/modules/Oficinas/View/create.php';
}

    public function edit($id)
{
    SecurityHelper::requireAuth();
    $office = $this->officeModel->find($id);
    $authUser = SecurityHelper::getAuthUser();

    if (!$office) {
        header("Location: /SGA-SEBANA/public/oficinas");
        exit;
    }

    if ($_POST) {
        $data = [
            'codigo' => trim($_POST['codigo'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'direccion' => trim($_POST['direccion'] ?? ''),
            'provincia' => trim($_POST['provincia'] ?? ''),
            'canton' => trim($_POST['canton'] ?? ''),
            'distrito' => trim($_POST['distrito'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'horario_atencion' => trim($_POST['horario_atencion'] ?? ''),
            'responsable' => trim($_POST['responsable'] ?? ''),
            'coordenadas_gps' => trim($_POST['coordenadas_gps'] ?? ''),
            'observaciones' => trim($_POST['observaciones'] ?? '')
        ];

        $this->officeModel->updateOffice($id, $data);
        header("Location: /SGA-SEBANA/public/oficinas");
        exit;
    }

    require BASE_PATH . '/app/modules/Oficinas/View/edit.php';
}

    public function toggleStatus($id)
    {
        SecurityHelper::requireAuth();
        $this->officeModel->toggleStatus($id);
        header("Location: /SGA-SEBANA/public/oficinas");
        exit;
    }

    /*
    public function delete($id)
    {
        SecurityHelper::requireAuth();
        $this->officeModel->deleteOffice($id);
        header("Location: /SGA-SEBANA/public/oficinas");
        exit;
    }
        */



}