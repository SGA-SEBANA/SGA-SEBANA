<?php

namespace App\Modules\Usuarios\Controllers;

use App\Core\ControllerBase;
use App\Modules\Usuarios\Models\User;
use App\Modules\Usuarios\Models\Role;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Usuarios\Helpers\SecurityHelper;

/**
 * UsersController - CRUD for users management
 */
class UsersController extends ControllerBase
{
    private User $userModel;
    private Role $roleModel;
    private Bitacora $bitacora;

    public function __construct()
    {
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->bitacora = new Bitacora();
    }

    /**
     * List all users
     */
    public function index(): void
    {
        SecurityHelper::requireAuth();

        $users = $this->userModel->getAllWithRoles();
        $authUser = SecurityHelper::getAuthUser();

        $this->view('users/list', [
            'title' => 'Gestión de Usuarios - SGA-SEBANA',
            'users' => $users,
            'authUser' => $authUser,
            'success' => $_SESSION['success_message'] ?? null,
            'error' => $_SESSION['error_message'] ?? null,
        ]);

        unset($_SESSION['success_message'], $_SESSION['error_message']);
    }

    /**
     * Show create user form
     */
    public function create(): void
    {
        SecurityHelper::requireAuth();

        $roles = $this->roleModel->getActive();
        $authUser = SecurityHelper::getAuthUser();

        $this->view('users/form', [
            'title' => 'Nuevo Usuario - SGA-SEBANA',
            'action' => 'create',
            'user' => null,
            'roles' => $roles,
            'authUser' => $authUser,
            'csrf_token' => SecurityHelper::getCsrfToken(),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? [],
        ]);

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    /**
     * Store new user
     */
    public function store(): void
    {
        SecurityHelper::requireAuth();

        if (!SecurityHelper::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error_message'] = 'Token de seguridad inválido.';
            $this->redirect('/SGA-SEBANA/public/users/create');
            return;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol_id' => (int) ($_POST['rol_id'] ?? 0),
        ];

        $errors = $this->validateUserData($data, null);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $data;
            $this->redirect('/SGA-SEBANA/public/users/create');
            return;
        }

        $hashedPassword = SecurityHelper::hashPassword($data['password']);

        $userId = $this->userModel->create([
            'username' => $data['username'],
            'correo' => $data['correo'],
            'contrasena' => $hashedPassword,
            'nombre_completo' => $data['nombre_completo'],
            'telefono' => $data['telefono'] ?: null,
            'rol_id' => $data['rol_id'],
            'estado' => 'activo',
            'debe_cambiar_contrasena' => true,
        ]);

        $this->bitacora->log([
            'accion' => 'CREATE',
            'modulo' => 'usuarios',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Usuario '{$data['username']}' creado",
            'datos_nuevos' => [
                'username' => $data['username'],
                'correo' => $data['correo'],
                'nombre_completo' => $data['nombre_completo'],
                'rol_id' => $data['rol_id'],
            ],
        ]);

        $_SESSION['success_message'] = 'Usuario creado exitosamente.';
        $this->redirect('/SGA-SEBANA/public/users');
    }

    /**
     * Show user details
     */
    public function show(string $id): void
    {
        SecurityHelper::requireAuth();

        $user = $this->userModel->findWithRole((int) $id);

        if (!$user) {
            $_SESSION['error_message'] = 'Usuario no encontrado.';
            $this->redirect('/SGA-SEBANA/public/users');
            return;
        }

        $authUser = SecurityHelper::getAuthUser();

        $this->view('users/show', [
            'title' => 'Detalle de Usuario - SGA-SEBANA',
            'user' => $user,
            'authUser' => $authUser,
        ]);
    }

    /**
     * Show edit user form
     */
    public function edit(string $id): void
    {
        SecurityHelper::requireAuth();

        $user = $this->userModel->find((int) $id);

        if (!$user) {
            $_SESSION['error_message'] = 'Usuario no encontrado.';
            $this->redirect('/SGA-SEBANA/public/users');
            return;
        }

        $roles = $this->roleModel->getActive();
        $authUser = SecurityHelper::getAuthUser();

        $this->view('users/form', [
            'title' => 'Editar Usuario - SGA-SEBANA',
            'action' => 'edit',
            'user' => $user,
            'roles' => $roles,
            'authUser' => $authUser,
            'csrf_token' => SecurityHelper::getCsrfToken(),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? [],
            'mustChangePassword' => $_SESSION['must_change_password'] ?? false,
        ]);

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
    }

    /**
     * Update user
     */
    public function update(string $id): void
    {
        SecurityHelper::requireAuth();

        $userId = (int) $id;

        if (!SecurityHelper::validateCsrfToken($_POST['_csrf_token'] ?? '')) {
            $_SESSION['error_message'] = 'Token de seguridad inválido.';
            $this->redirect("/SGA-SEBANA/public/users/{$userId}/edit");
            return;
        }

        $existingUser = $this->userModel->find($userId);
        if (!$existingUser) {
            $_SESSION['error_message'] = 'Usuario no encontrado.';
            $this->redirect('/SGA-SEBANA/public/users');
            return;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'correo' => trim($_POST['correo'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'password_confirm' => $_POST['password_confirm'] ?? '',
            'nombre_completo' => trim($_POST['nombre_completo'] ?? ''),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'rol_id' => (int) ($_POST['rol_id'] ?? 0),
        ];

        $errors = $this->validateUserData($data, $userId);

        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_old'] = $data;
            $this->redirect("/SGA-SEBANA/public/users/{$userId}/edit");
            return;
        }

        $updateData = [
            'username' => $data['username'],
            'correo' => $data['correo'],
            'nombre_completo' => $data['nombre_completo'],
            'telefono' => $data['telefono'] ?: null,
            'rol_id' => $data['rol_id'],
        ];

        if (!empty($data['password'])) {
            $updateData['contrasena'] = SecurityHelper::hashPassword($data['password']);
            $updateData['debe_cambiar_contrasena'] = false;
        }

        $oldData = [
            'username' => $existingUser['username'],
            'correo' => $existingUser['correo'],
            'nombre_completo' => $existingUser['nombre_completo'],
            'rol_id' => $existingUser['rol_id'],
        ];

        $this->userModel->update($userId, $updateData);

        $this->bitacora->log([
            'accion' => 'UPDATE',
            'modulo' => 'usuarios',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Usuario '{$data['username']}' actualizado",
            'datos_anteriores' => $oldData,
            'datos_nuevos' => [
                'username' => $data['username'],
                'correo' => $data['correo'],
                'nombre_completo' => $data['nombre_completo'],
                'rol_id' => $data['rol_id'],
                'password_changed' => !empty($data['password']),
            ],
        ]);

        unset($_SESSION['must_change_password']);

        $_SESSION['success_message'] = 'Usuario actualizado exitosamente.';
        $this->redirect('/SGA-SEBANA/public/users');
    }

    /**
     * Toggle user status (activate/deactivate)
     */
    public function toggleStatus(string $id): void
    {
        SecurityHelper::requireAuth();

        $userId = (int) $id;
        $user = $this->userModel->find($userId);

        if (!$user) {
            $_SESSION['error_message'] = 'Usuario no encontrado.';
            $this->redirect('/SGA-SEBANA/public/users');
            return;
        }

        $authUserId = SecurityHelper::getAuthUserId();
        if ($userId === $authUserId) {
            $_SESSION['error_message'] = 'No puede desactivar su propia cuenta.';
            $this->redirect('/SGA-SEBANA/public/users');
            return;
        }

        $oldStatus = $user['estado'];
        $this->userModel->toggleStatus($userId);

        $updatedUser = $this->userModel->find($userId);
        $newStatus = $updatedUser['estado'];

        $action = $newStatus === 'activo' ? 'reactivado' : 'desactivado';

        $this->bitacora->log([
            'accion' => 'STATUS_CHANGE',
            'modulo' => 'usuarios',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Usuario '{$user['username']}' {$action}",
            'datos_anteriores' => ['estado' => $oldStatus],
            'datos_nuevos' => ['estado' => $newStatus],
        ]);

        $_SESSION['success_message'] = "Usuario {$action} exitosamente.";
        $this->redirect('/SGA-SEBANA/public/users');
    }

    /**
     * Show bitacora (audit log)
     */
    public function bitacora(): void
    {
        SecurityHelper::requireAuth();

        $logs = $this->bitacora->getRecent(200);
        $authUser = SecurityHelper::getAuthUser();

        $this->view('bitacora/list', [
            'title' => 'Bitácora de Auditoría - SGA-SEBANA',
            'logs' => $logs,
            'authUser' => $authUser,
        ]);
    }

    /**
     * Validate user data
     */
    private function validateUserData(array $data, ?int $excludeId): array
    {
        $errors = [];

        if (empty($data['username'])) {
            $errors['username'] = 'El nombre de usuario es requerido.';
        } elseif (strlen($data['username']) < 3) {
            $errors['username'] = 'El nombre de usuario debe tener al menos 3 caracteres.';
        } elseif ($this->userModel->existsUsername($data['username'], $excludeId)) {
            $errors['username'] = 'Este nombre de usuario ya está en uso.';
        }

        if (empty($data['correo'])) {
            $errors['correo'] = 'El correo electrónico es requerido.';
        } elseif (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors['correo'] = 'El correo electrónico no es válido.';
        } elseif ($this->userModel->existsEmail($data['correo'], $excludeId)) {
            $errors['correo'] = 'Este correo electrónico ya está en uso.';
        }

        if ($excludeId === null || !empty($data['password'])) {
            if (empty($data['password'])) {
                $errors['password'] = 'La contraseña es requerida.';
            } else {
                $passwordValidation = SecurityHelper::validatePasswordStrength($data['password']);
                if (!$passwordValidation['valid']) {
                    $errors['password'] = implode(' ', $passwordValidation['errors']);
                }

                if ($data['password'] !== $data['password_confirm']) {
                    $errors['password_confirm'] = 'Las contraseñas no coinciden.';
                }
            }
        }

        if (empty($data['nombre_completo'])) {
            $errors['nombre_completo'] = 'El nombre completo es requerido.';
        }

        if (empty($data['rol_id'])) {
            $errors['rol_id'] = 'Debe seleccionar un rol.';
        }

        return $errors;
    }
}
