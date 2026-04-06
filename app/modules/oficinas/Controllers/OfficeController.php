<?php

namespace App\Modules\Oficinas\Controllers;

use App\Modules\Oficinas\Models\OfficeModel;
use App\Modules\Usuarios\Helpers\SecurityHelper;
use App\Modules\Visitas\Models\Notification;

class OfficeController
{
    private OfficeModel $officeModel;
    private Notification $notiModel;

    public function __construct()
    {
        $this->officeModel = new OfficeModel();
        $this->notiModel = new Notification();
    }

    public function index()
    {
        SecurityHelper::requireAuth();
        $offices = $this->officeModel->getAll();
        $authUser = SecurityHelper::getAuthUser();
        require BASE_PATH . '/app/modules/oficinas/view/index.php';
    }

    public function create()
    {
        SecurityHelper::requireAuth();
        $authUser = SecurityHelper::getAuthUser();
        $errors = [];
        $old = [];
        $office = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old = $this->collectPayload($_POST);
            $errors = $this->validate($old);

            if (empty($errors)) {
                $data = $old;
                $data['activo'] = 1;

                $newOfficeId = $this->officeModel->createOffice($data);
                if ($newOfficeId <= 0) {
                    $errors['general'] = 'No se pudo registrar la oficina. Intente de nuevo.';
                } else {
                    $this->notiModel->createNotification(
                        1,
                        'sistema',
                        'oficinas',
                        'Nueva Oficina Registrada',
                        "Se registro la oficina: {$data['nombre']}",
                        'oficina',
                        $newOfficeId,
                        "/SGA-SEBANA/public/oficinas"
                    );

                    header('Location: /SGA-SEBANA/public/oficinas?success=created');
                    exit;
                }
            }
        }

        require BASE_PATH . '/app/modules/oficinas/view/create.php';
    }

    public function edit($id)
    {
        SecurityHelper::requireAuth();
        $authUser = SecurityHelper::getAuthUser();
        $office = $this->officeModel->find($id);

        if (!$office) {
            header('Location: /SGA-SEBANA/public/oficinas');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->collectPayload($_POST);
            $this->officeModel->updateOffice($id, $data);

            $this->notiModel->createNotification(
                1,
                'sistema',
                'oficinas',
                'Oficina Editada',
                "Se actualizaron los datos de la oficina ID {$id} ({$data['nombre']})",
                'oficina',
                (int) $id,
                "/SGA-SEBANA/public/oficinas/edit/{$id}"
            );

            header('Location: /SGA-SEBANA/public/oficinas?success=updated');
            exit;
        }

        require BASE_PATH . '/app/modules/oficinas/view/edit.php';
    }

    public function toggleStatus($id)
    {
        SecurityHelper::requireAuth();
        $this->officeModel->toggleStatus($id);

        $office = $this->officeModel->find($id);
        $nuevoEstado = ((int) ($office['activo'] ?? 0) === 1) ? 'Activa' : 'Inactiva';

        $this->notiModel->createNotification(
            1,
            'sistema',
            'oficinas',
            'Estado de Oficina Cambiado',
            "La oficina ID {$id} ahora esta en estado: {$nuevoEstado}",
            'oficina',
            (int) $id,
            '/SGA-SEBANA/public/oficinas'
        );

        header('Location: /SGA-SEBANA/public/oficinas?success=toggled');
        exit;
    }

    private function collectPayload(array $source): array
    {
        $payload = [
            'codigo' => trim((string) ($source['codigo'] ?? '')),
            'nombre' => trim((string) ($source['nombre'] ?? '')),
            'direccion' => trim((string) ($source['direccion'] ?? '')),
            'provincia' => trim((string) ($source['provincia'] ?? '')),
            'canton' => trim((string) ($source['canton'] ?? '')),
            'distrito' => trim((string) ($source['distrito'] ?? '')),
            'telefono' => trim((string) ($source['telefono'] ?? '')),
            'correo' => trim((string) ($source['correo'] ?? '')),
            'horario_atencion' => trim((string) ($source['horario_atencion'] ?? '')),
            'responsable' => trim((string) ($source['responsable'] ?? '')),
            'coordenadas_gps' => trim((string) ($source['coordenadas_gps'] ?? '')),
            'observaciones' => trim((string) ($source['observaciones'] ?? '')),
        ];

        if (array_key_exists('activo', $source)) {
            $payload['activo'] = ((int) ($source['activo'] ?? 0) === 1) ? 1 : 0;
        }

        return $payload;
    }

    private function validate(array $data): array
    {
        $errors = [];

        if ($data['nombre'] === '') {
            $errors['nombre'] = 'El nombre de la oficina es obligatorio.';
        }

        if ($data['correo'] !== '' && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo no tiene un formato valido.';
        }

        return $errors;
    }
}
