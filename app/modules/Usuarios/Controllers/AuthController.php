<?php

namespace App\Modules\Usuarios\Controllers;

use App\Core\ControllerBase;
use App\Modules\Usuarios\Models\User;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Usuarios\Helpers\SecurityHelper;

/**
 * AuthController - Handles authentication (login/logout)
 */
class AuthController extends ControllerBase
{
    private User $userModel;
    private Bitacora $bitacora;

    public function __construct()
    {
        $this->userModel = new User();
        $this->bitacora = new Bitacora();
    }

    private function resolvePostLoginRedirect(array $user): string
    {
        $roleName = strtolower(trim((string) ($user['rol_nombre'] ?? '')));

        switch ($roleName) {
            case 'administrador general':
                return '/SGA-SEBANA/public/home';
            case 'administrador de rrll':
                return '/SGA-SEBANA/public/casos-rrll';
            case 'administrador de solicitudes':
                return '/SGA-SEBANA/public/admin/visit-requests';
            case 'operador':
                return '/SGA-SEBANA/public/afiliados';
            case 'auditor':
                return '/SGA-SEBANA/public/bitacora';
            case 'empleado de sebana':
            case 'empleado sebana':
                return '/SGA-SEBANA/public/vacaciones';
            case 'consulta':
            default:
                return '/SGA-SEBANA/public/visit-requests';
        }
    }

    /**
     * Show login form
     */
    public function showLogin(): void
    {
        if (SecurityHelper::isAuthenticated()) {
            $authUser = SecurityHelper::getAuthUser() ?? [];
            $this->redirect($this->resolvePostLoginRedirect($authUser));
            return;
        }

        $this->view('auth/login', [
            'title' => 'Iniciar Sesión - SGA-SEBANA',
            'csrf_token' => SecurityHelper::getCsrfToken(),
            'error' => $_SESSION['login_error'] ?? null,
        ]);

        unset($_SESSION['login_error']);
    }

    /**
     * Process login form
     */
    public function login(): void
    {
        $csrfToken = $_POST['_csrf_token'] ?? '';
        if (!SecurityHelper::validateCsrfToken($csrfToken)) {
            $_SESSION['login_error'] = 'Token de seguridad inválido. Por favor, intente de nuevo.';
            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor, complete todos los campos.';
            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        $user = $this->userModel->findByUsername($username);

        if (!$user) {
            $this->bitacora->logFailedLogin(null, $username, 'Usuario no encontrado');
            $_SESSION['login_error'] = 'Credenciales inválidas.';
            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        if ($user['bloqueado'] || $user['estado'] === 'bloqueado') {
            $this->bitacora->logFailedLogin($user['id'], $username, 'Usuario bloqueado');
            $_SESSION['login_error'] = 'Su cuenta está bloqueada. Contacte al administrador.';
            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        if ($user['estado'] === 'inactivo') {
            $this->bitacora->logFailedLogin($user['id'], $username, 'Usuario inactivo');
            $_SESSION['login_error'] = 'Su cuenta está inactiva. Contacte al administrador.';
            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        $userWithRole = $this->userModel->findWithRole((int) $user['id']) ?: [];
        $roleName = strtolower(trim((string) ($userWithRole['rol_nombre'] ?? '')));
        if ($roleName === 'consulta') {
            $affiliateStatus = $this->userModel->getLinkedAffiliateStatus((int) $user['id']);
            if (in_array($affiliateStatus, ['inactivo', 'suspendido', 'removido'], true)) {
                $this->bitacora->logFailedLogin($user['id'], $username, 'Afiliado inactivo o suspendido');
                $_SESSION['login_error'] = 'Su afiliacion se encuentra inactiva o suspendida. Contacte al administrador.';
                $this->redirect('/SGA-SEBANA/public/login');
                return;
            }
        }

        if (!password_verify($password, $user['contrasena'])) {
            $attempts = $this->userModel->incrementFailedAttempts($user['id']);
            $this->bitacora->logFailedLogin($user['id'], $username, 'Contraseña incorrecta');

            if ($attempts >= 5) {
                $_SESSION['login_error'] = 'Su cuenta ha sido bloqueada por múltiples intentos fallidos.';
            } else {
                $remaining = 5 - $attempts;
                $_SESSION['login_error'] = "Contraseña incorrecta. Intentos restantes: {$remaining}";
            }

            $this->redirect('/SGA-SEBANA/public/login');
            return;
        }

        SecurityHelper::regenerateSession();

        $this->userModel->resetFailedAttempts($user['id']);

        $this->userModel->updateLastAccess($user['id']);

        $permisos = $userWithRole['permisos'] ?? null;

        if (is_string($permisos)) {
            $decoded = json_decode($permisos, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $permisos = $decoded;
            }
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_authenticated'] = true;
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['correo'],
            'nombre_completo' => $user['nombre_completo'],
            'rol_id' => $user['rol_id'],
            'rol_nombre' => $userWithRole['rol_nombre'] ?? 'Sin rol',
            'nivel_acceso' => $userWithRole['nivel_acceso'] ?? 'basico',
            'permisos' => $permisos,
        ];

        $this->bitacora->logLogin($user['id'], $user['username']);

        if ($user['debe_cambiar_contrasena']) {
            $_SESSION['must_change_password'] = true;
            $this->redirect('/SGA-SEBANA/public/users/' . $user['id'] . '/edit');
            return;
        }

        unset($_SESSION['must_change_password']);

        $this->redirect($this->resolvePostLoginRedirect($_SESSION['user']));
    }

    /**
     * Logout user
     */
    public function logout(): void
    {
        $userId = SecurityHelper::getAuthUserId();
        $user = SecurityHelper::getAuthUser();

        if ($userId && $user) {
            $this->bitacora->logLogout($userId, $user['username']);
        }

        SecurityHelper::destroySession();

        $this->redirect('/SGA-SEBANA/public/login');
    }
}
