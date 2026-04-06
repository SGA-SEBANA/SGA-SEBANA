<?php
namespace App\Modules\Afiliados\Controllers;

use App\Core\ControllerBase;
use App\Modules\Afiliados\Models\Afiliados;
use App\Modules\Usuarios\Helpers\AffiliateAccountProvisioner;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Visitas\Models\Notification;
use App\Helpers\Paginator;

class AfiliadosController extends ControllerBase
{
    protected $notiModel;

    public function __construct()
    {
        $this->notiModel = new Notification();
    }

  public function index()
{

    $modelo = new Afiliados();

    $filtros = [
        'busqueda' => trim($_GET['q'] ?? ''),
        'estado' => $_GET['estado'] ?? '',
        'oficina_id' => $_GET['oficina_id'] ?? ''
    ];


    $pagination = Paginator::make(
        $modelo,'getAll',$filtros,
        $_GET['page'] ?? 1,10
    );

    $afiliados = $pagination['data'];
    $page = $pagination['page'];
    $totalPaginas = $pagination['totalPaginas'];

    $oficinas = $modelo->getOficinas();

    $data = [
        'titulo' => 'Gestion de Afiliados',
        'afiliados' => $afiliados,
        'oficinas' => $oficinas,
        'filtros' => $filtros,
        'page' => $page,
        'totalPaginas' => $totalPaginas,
        'success' => $_GET['success'] ?? null
    ];

    $this->view('index', $data);
}

    public function create()
    {
        $modelo = new Afiliados();
        $autoUserNotice = $_SESSION['afiliado_user_notice'] ?? null;
        unset($_SESSION['afiliado_user_notice']);

        $data = [
            'titulo' => 'Registrar Nuevo Afiliado',
            'categorias' => $modelo->getCategorias(),
            'oficinas' => $modelo->getOficinas(),
            'success' => isset($_GET['status']) && $_GET['status'] === 'success' ? 'Afiliado registrado correctamente.' : null,
            'error' => null,
            'auto_user_notice' => $autoUserNotice
        ];

        $this->view('create', $data);
    }

    public function store()
    {
        try {
            $datos = $this->limpiarDatos($_POST);
            $modelo = new Afiliados();

            if ($datos['cedula'] === '') {
                echo "<script>alert('Error: Cedula invalida. Use solo numeros (formato unico).'); window.history.back();</script>";
                return;
            }

            if ($modelo->existeCedula($datos['cedula'])) {
                echo "<script>alert('Error: Cedula duplicada.'); window.history.back();</script>";
                return;
            }

            if (!empty($datos['correo']) && $modelo->existeCorreo($datos['correo'])) {
                echo "<script>alert('Error: Correo duplicado.'); window.history.back();</script>";
                return;
            }

            $nuevoId = (int) $modelo->create($datos);
            if ($nuevoId <= 0) {
                throw new \Exception('Fallo en la insercion en base de datos.');
            }

            $provisioner = new AffiliateAccountProvisioner();
            $provision = $provisioner->provision([
                'cedula' => $datos['cedula'],
                'correo' => $datos['correo'] ?? '',
                'nombre' => $datos['nombre'] ?? '',
                'apellido1' => $datos['apellido1'] ?? '',
                'apellido2' => $datos['apellido2'] ?? '',
                'telefono' => $datos['telefono'] ?? ''
            ]);

            $_SESSION['afiliado_user_notice'] = $this->buildAutoUserNotice($provision);

            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'afiliados',
                'entidad' => 'afiliado',
                'entidad_id' => $nuevoId,
                'descripcion' => "Creacion de afiliado cedula: {$datos['cedula']}",
                'datos_nuevos' => $datos
            ]);

            $this->notiModel->createNotification(
                1,
                'sistema',
                'afiliados',
                'Nuevo Afiliado',
                "Se registro exitosamente a: {$datos['nombre']} {$datos['apellido1']}",
                'afiliado',
                $nuevoId,
                "/SGA-SEBANA/public/afiliados/edit/{$nuevoId}"
            );

            header('Location: /SGA-SEBANA/public/afiliados/create?status=success');
            exit;
        } catch (\Exception $e) {
            $this->notiModel->createNotification(
                1,
                'error',
                'critico',
                'Error al Registrar',
                'Error al procesar alta de afiliado: ' . $e->getMessage(),
                'error_log',
                0,
                null,
                'alta'
            );

            echo 'Error al guardar: ' . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $modelo = new Afiliados();
        $afiliado = $modelo->getById($id);

        if (!$afiliado) {
            echo 'Afiliado no encontrado.';
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
        try {
            $datos = $this->limpiarDatos($_POST);
            $modelo = new Afiliados();
            $anterior = $modelo->getById($id);

            if ($datos['cedula'] === '') {
                echo "<script>alert('Error: Cedula invalida. Use solo numeros (formato unico).'); window.history.back();</script>";
                return;
            }

            if ($modelo->existeCedula($datos['cedula'], $id)) {
                echo "<script>alert('Error: Esa cedula ya pertenece a otro afiliado.'); window.history.back();</script>";
                return;
            }

            if (!empty($datos['correo']) && $modelo->existeCorreo($datos['correo'], $id)) {
                echo "<script>alert('Error: Ese correo ya pertenece a otro afiliado.'); window.history.back();</script>";
                return;
            }

            if (!$modelo->update($id, $datos)) {
                throw new \Exception("No se pudo actualizar el registro ID: {$id}");
            }

            $bitacora = new Bitacora();
            $bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'afiliados',
                'entidad' => 'afiliado',
                'entidad_id' => $id,
                'descripcion' => "Actualizacion de afiliado ID: {$id}",
                'datos_anteriores' => $anterior,
                'datos_nuevos' => $datos
            ]);

            $this->notiModel->createNotification(
                1,
                'sistema',
                'afiliados',
                'Afiliado Editado',
                "Se actualizaron los datos de: {$datos['nombre']} {$datos['apellido1']}. Puesto: {$datos['puesto_actual']}",
                'afiliado',
                $id,
                "/SGA-SEBANA/public/afiliados/edit/{$id}"
            );

            header('Location: /SGA-SEBANA/public/afiliados?success=Afiliado actualizado correctamente');
            exit;
        } catch (\Exception $e) {
            $this->notiModel->createNotification(
                1,
                'error',
                'critico',
                'Error al Editar',
                "Error al actualizar afiliado ID {$id}: " . $e->getMessage(),
                'error_log',
                $id,
                null,
                'media'
            );

            echo 'Error al actualizar.';
        }
    }

    public function toggle($id)
    {
        try {
            $modelo = new Afiliados();
            $nuevoEstado = $modelo->toggleStatus($id);

            if ($nuevoEstado) {
                $bitacora = new Bitacora();
                $bitacora->log([
                    'accion' => 'STATUS_CHANGE',
                    'modulo' => 'afiliados',
                    'entidad' => 'afiliado',
                    'entidad_id' => $id,
                    'descripcion' => "Cambio de estado a: {$nuevoEstado}",
                    'resultado' => 'exitoso'
                ]);

                $this->notiModel->createNotification(
                    1,
                    'sistema',
                    'afiliados',
                    'Estado Cambiado',
                    "El afiliado ID {$id} ahora esta en estado: {$nuevoEstado}",
                    'afiliado',
                    $id,
                    '/SGA-SEBANA/public/afiliados'
                );

                header('Location: /SGA-SEBANA/public/afiliados?success=Estado actualizado correctamente');
                exit;
            }
        } catch (\Exception $e) {
            $this->notiModel->createNotification(1, 'error', 'critico', 'Error Status', $e->getMessage(), 'error', $id);
            echo 'Error al cambiar el estado.';
        }
    }

    public function procesarBaja($id)
    {
        try {
            $modelo = new Afiliados();
            $dataBaja = [
                'fecha_baja' => date('Y-m-d'),
                'motivo_baja' => $_POST['motivo_baja'],
                'tipo_baja' => $_POST['tipo_baja']
            ];

            if ($modelo->registrarBaja($id, $dataBaja)) {
                $this->notiModel->createNotification(
                    1,
                    'sistema',
                    'afiliados',
                    'Afiliado Desactivado',
                    "Se proceso la baja del afiliado ID {$id}. Motivo: {$dataBaja['motivo_baja']}",
                    'afiliado',
                    $id,
                    '/SGA-SEBANA/public/afiliados'
                );

                header('Location: /SGA-SEBANA/public/afiliados?success=Afiliado desactivado correctamente');
                exit;
            }
        } catch (\Exception $e) {
            $this->notiModel->createNotification(1, 'error', 'critico', 'Error en Baja', $e->getMessage(), 'error', $id);
            header('Location: /SGA-SEBANA/public/afiliados?error=fallo_baja');
        }
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

    private function limpiarDatos($post)
    {
        $contactoEmergencia = [
            'nombre' => trim($post['emergencia_nombre'] ?? ''),
            'telefono' => trim($post['emergencia_telefono'] ?? ''),
            'relacion' => trim($post['emergencia_relacion'] ?? '')
        ];

        return [
            'nombre' => trim($post['nombre'] ?? ''),
            'apellido1' => trim($post['apellido1'] ?? ''),
            'apellido2' => trim($post['apellido2'] ?? ''),
            'cedula' => $this->normalizeCedula($post['cedula'] ?? ''),
            'genero' => trim($post['genero'] ?? ''),
            'fecha_nacimiento' => trim($post['fecha_nacimiento'] ?? ''),
            'correo' => trim($post['correo'] ?? ''),
            'telefono' => trim($post['telefono'] ?? ''),
            'telefono_secundario' => trim($post['telefono_secundario'] ?? ''),
            'direccion' => trim($post['direccion'] ?? ''),
            'categoria_id' => !empty($post['categoria_id']) ? (int) $post['categoria_id'] : null,
            'oficina_id' => !empty($post['oficina_id']) ? (int) $post['oficina_id'] : null,
            'puesto_actual' => trim($post['puesto_actual'] ?? ''),
            'datos_contacto_emergencia' => json_encode($contactoEmergencia),
            'observaciones' => trim($post['observaciones'] ?? '')
        ];
    }

    private function normalizeCedula($cedula): string
    {
        return preg_replace('/[^0-9]/', '', trim((string) $cedula)) ?? '';
    }

    private function buildAutoUserNotice(array $provision): array
    {
        if (!($provision['success'] ?? false)) {
            return [
                'type' => 'warning',
                'message' => 'El afiliado se registro, pero no se pudo crear el usuario automatico.',
                'detail' => $provision['error'] ?? 'Error no especificado.'
            ];
        }

        if (($provision['created'] ?? false) === true) {
            return [
                'type' => 'info',
                'message' => 'Usuario afiliado creado automaticamente.',
                'username' => $provision['username'] ?? '',
                'correo' => $provision['correo'] ?? '',
                'temp_password' => $provision['temp_password'] ?? ''
            ];
        }

        return [
            'type' => 'secondary',
            'message' => 'El afiliado ya tenia usuario, no se creo uno nuevo.',
            'username' => $provision['username'] ?? '',
            'correo' => $provision['correo'] ?? ''
        ];
    }

    public function reporteGenero()
    {
        $modelo = new Afiliados();
        $genero = strtolower(trim((string) ($_GET['genero'] ?? '')));
        $permitidos = ['masculino', 'femenino', 'otro', 'prefiero_no_decir'];

        if ($genero !== '' && !in_array($genero, $permitidos, true)) {
            $genero = '';
        }

        $rows = $modelo->getReporteGenero($genero !== '' ? $genero : null);

        $bitacora = new Bitacora();
        $bitacora->log([
            'accion' => 'READ',
            'modulo' => 'afiliados',
            'entidad' => 'reporte_genero',
            'descripcion' => 'Generacion de reporte de afiliados por genero',
            'datos_nuevos' => ['genero' => $genero !== '' ? $genero : 'todos']
        ]);

        $slugGenero = $genero !== '' ? $genero : 'todos';
        $filename = 'reporte_afiliados_genero_' . $slugGenero . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        if ($output === false) {
            echo 'No se pudo generar el archivo.';
            return;
        }

        // BOM UTF-8 para compatibilidad con Excel
        fwrite($output, "\xEF\xBB\xBF");
        fputcsv($output, ['Cedula', 'Nombre completo', 'Genero', 'Categoria', 'Oficina', 'Telefono', 'Correo', 'Estado']);

        foreach ($rows as $row) {
            $generoRaw = strtolower((string) ($row['genero'] ?? ''));
            $generoLabel = match ($generoRaw) {
                'masculino' => 'Masculino',
                'femenino' => 'Femenino',
                'otro' => 'Otro',
                default => 'Prefiero no decir'
            };

            fputcsv($output, [
                (string) ($row['cedula'] ?? ''),
                (string) ($row['nombre_completo'] ?? ''),
                $generoLabel,
                (string) ($row['categoria_nombre'] ?? 'Sin categoria'),
                (string) ($row['oficina_nombre'] ?? 'Sin oficina'),
                (string) ($row['telefono'] ?? ''),
                (string) ($row['correo'] ?? ''),
                ucfirst((string) ($row['estado'] ?? ''))
            ]);
        }

        fclose($output);
        exit;
    }


    public function buscarAfiliado()
{

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    $cedula = $this->normalizeCedula($_GET['cedula'] ?? '');

    $model = new \App\Modules\Afiliados\Models\Afiliados();
    $resultados = $model->searchByCedula($cedula);

    header('Content-Type: application/json');
    echo json_encode($resultados);
}
}
