<?php

namespace App\Modules\Usuarios\Helpers;

class AccessControl
{
    public static function authorize(string $uri, string $method): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $uri = self::normalizeUri($uri);
        $method = strtoupper($method);

        if (self::matchesAny(self::publicRules(), $uri, $method)) {
            return ['allowed' => true];
        }

        if (!SecurityHelper::isAuthenticated()) {
            $_SESSION['login_error'] = 'Debe iniciar sesion para acceder al sistema.';
            return [
                'allowed' => false,
                'redirect' => '/SGA-SEBANA/public/login'
            ];
        }

        if (self::isAuditor() && !self::matchesAny(self::auditorAllowedRules(), $uri, $method)) {
            return [
                'allowed' => false,
                'redirect' => self::unauthorizedRedirect()
            ];
        }

        foreach (self::protectedRules() as $rule) {
            if (!self::matchRule($rule, $uri, $method)) {
                continue;
            }

            $required = self::levelRank($rule['min'] ?? 'basico');
            if (self::currentLevelRank() < $required) {
                return [
                    'allowed' => false,
                    'redirect' => self::unauthorizedRedirect()
                ];
            }

            if (!empty($rule['roles']) && !self::hasAnyRole($rule['roles'])) {
                return [
                    'allowed' => false,
                    'redirect' => self::unauthorizedRedirect()
                ];
            }

            if (!empty($rule['exclude_roles']) && self::hasAnyRole((array) $rule['exclude_roles'])) {
                return [
                    'allowed' => false,
                    'redirect' => self::unauthorizedRedirect()
                ];
            }

            if (!empty($rule['affiliate_only']) && !self::isAffiliateRole()) {
                return [
                    'allowed' => false,
                    'redirect' => self::unauthorizedRedirect()
                ];
            }

            if (!empty($rule['employee_only']) && !self::isEmployeeRole()) {
                return [
                    'allowed' => false,
                    'redirect' => self::unauthorizedRedirect()
                ];
            }

            return ['allowed' => true];
        }

        return [
            'allowed' => false,
            'redirect' => self::unauthorizedRedirect()
        ];
    }

    public static function currentLevelRank(): int
    {
        $nivel = $_SESSION['user']['nivel_acceso'] ?? 'basico';
        return self::levelRank($nivel);
    }

    public static function hasLevel(string $minLevel): bool
    {
        return self::currentLevelRank() >= self::levelRank($minLevel);
    }

    public static function isAffiliateRole(): bool
    {
        return self::currentRoleKey() === 'consulta';
    }

    public static function isEmployeeRole(): bool
    {
        return self::currentRoleKey() === 'empleado_sebana';
    }

    public static function isAuditor(): bool
    {
        return self::currentRoleKey() === 'auditor';
    }

    public static function hasAnyRole(array $roles): bool
    {
        $current = self::currentRoleKey();
        if ($current === '') {
            return false;
        }

        foreach ($roles as $role) {
            if ($current === self::normalizeRoleKey((string) $role)) {
                return true;
            }
        }

        return false;
    }

    public static function currentRoleKey(): string
    {
        $roleName = (string) ($_SESSION['user']['rol_nombre'] ?? '');
        return self::normalizeRoleKey($roleName);
    }

    public static function levelRank($nivel): int
    {
        if (is_numeric($nivel)) {
            $n = (int) $nivel;
            if ($n >= 90) {
                return 4;
            }
            if ($n >= 50) {
                return 3;
            }
            if ($n >= 20) {
                return 2;
            }
            return 1;
        }

        $nivel = strtolower(trim((string) $nivel));
        switch ($nivel) {
            case 'total':
                return 4;
            case 'alto':
                return 3;
            case 'medio':
                return 2;
            case 'basico':
            default:
                return 1;
        }
    }

    private static function publicRules(): array
    {
        return [
            ['pattern' => '~^/login$~i', 'methods' => ['GET', 'POST']],
            ['pattern' => '~^/afiliarse(?:/.*)?$~i', 'methods' => ['GET', 'POST']],
            ['pattern' => '~^/asistente-afiliacion$~i', 'methods' => ['GET']],
            ['pattern' => '~^/carnets/validar/[^/]+$~i', 'methods' => ['GET']]
        ];
    }

    private static function auditorAllowedRules(): array
    {
        return [
            ['pattern' => '~^/$~i', 'methods' => ['GET']],
            ['pattern' => '~^/home$~i', 'methods' => ['GET']],
            ['pattern' => '~^/logout$~i', 'methods' => ['GET']],
            ['pattern' => '~^/bitacora(?:/.*)?$~i', 'methods' => ['GET']],
            ['pattern' => '~^/ReporteDeExclusionDeAfiliado(?:/.*)?$~i', 'methods' => ['GET']],
            ['pattern' => '~^/notificaciones/(read|archive|read-all)(?:/[^/]+)?$~i', 'methods' => ['GET']]
        ];
    }

    private static function protectedRules(): array
    {
        return [
            ['pattern' => '~^/$~i', 'methods' => ['GET'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_rrll', 'admin_solicitudes']],
            ['pattern' => '~^/home$~i', 'methods' => ['GET'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_rrll', 'admin_solicitudes']],
            ['pattern' => '~^/logout$~i', 'methods' => ['GET'], 'min' => 'basico'],
            ['pattern' => '~^/notificaciones/(read|archive|read-all)(?:/[^/]+)?$~i', 'methods' => ['GET'], 'min' => 'basico'],

            ['pattern' => '~^/users$~i', 'methods' => ['GET'], 'min' => 'total'],
            ['pattern' => '~^/users/create$~i', 'methods' => ['GET'], 'min' => 'total'],
            ['pattern' => '~^/users$~i', 'methods' => ['POST'], 'min' => 'total'],
            ['pattern' => '~^/users/\d+$~i', 'methods' => ['GET'], 'min' => 'total'],
            ['pattern' => '~^/users/\d+/toggle$~i', 'methods' => ['POST'], 'min' => 'total'],
            ['pattern' => '~^/users/\d+/edit$~i', 'methods' => ['GET'], 'min' => 'basico'],
            ['pattern' => '~^/users/\d+$~i', 'methods' => ['POST'], 'min' => 'basico'],
            ['pattern' => '~^/ui(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'total'],

            ['pattern' => '~^/casos-rrll(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_rrll']],

            ['pattern' => '~^/admin/visit-requests(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/admin/request-calendar$~i', 'methods' => ['GET'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/admin/visit-calendar-events$~i', 'methods' => ['GET'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/asistente-afiliacion/solicitudes(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/asistente-afiliacion/documento/[^/]+/[^/]+$~i', 'methods' => ['GET'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/ayudas/status/\d+$~i', 'methods' => ['POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/vacaciones/status/\d+$~i', 'methods' => ['POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],
            ['pattern' => '~^/viaticos/status$~i', 'methods' => ['POST'], 'min' => 'alto', 'roles' => ['admin_general', 'admin_solicitudes']],

            ['pattern' => '~^/junta/(create|edit/[^/]+|finalizar/[^/]+|activar/[^/]+|eliminar-documento/[^/]+)$~i', 'methods' => ['GET', 'POST'], 'min' => 'alto'],

            ['pattern' => '~^/bitacora(?:/.*)?$~i', 'methods' => ['GET'], 'min' => 'medio'],
            ['pattern' => '~^/ReporteDeExclusionDeAfiliado(?:/.*)?$~i', 'methods' => ['GET'], 'min' => 'medio'],
            ['pattern' => '~^/carnets/(emitir|descargar)/[^/]+$~i', 'methods' => ['GET'], 'min' => 'medio'],

            ['pattern' => '~^/afiliados(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'medio'],
            ['pattern' => '~^/(categorias|Categorias)(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'medio'],
            ['pattern' => '~^/oficinas(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'medio'],
            ['pattern' => '~^/puestos(?:/.*)?$~i', 'methods' => ['GET', 'POST'], 'min' => 'medio'],
            ['pattern' => '~^/junta(?:$|/history$|/documento/[^/]+$|/ver-documento/[^/]+$)$~i', 'methods' => ['GET'], 'min' => 'medio'],

            ['pattern' => '~^/visit-requests$~i', 'methods' => ['GET'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']],
            ['pattern' => '~^/visit-requests/create$~i', 'methods' => ['GET', 'POST'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']],
            ['pattern' => '~^/visit-requests/\d+/(reschedule|cancel)$~i', 'methods' => ['GET', 'POST'], 'min' => 'basico', 'affiliate_only' => true, 'exclude_roles' => ['empleado_sebana']],

            ['pattern' => '~^/ayudas(?:$|/show/\d+$|/archivo/\d+$)$~i', 'methods' => ['GET'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']],
            ['pattern' => '~^/ayudas/(create|store)$~i', 'methods' => ['GET', 'POST'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']],
            ['pattern' => '~^/ayudas/(cancel|evidence)/\d+$~i', 'methods' => ['POST'], 'min' => 'basico', 'affiliate_only' => true, 'exclude_roles' => ['empleado_sebana']],

            ['pattern' => '~^/vacaciones(?:$|/show/\d+$)$~i', 'methods' => ['GET'], 'min' => 'basico', 'roles' => ['admin_general', 'admin_solicitudes', 'empleado_sebana']],
            ['pattern' => '~^/vacaciones/(create|store)$~i', 'methods' => ['GET', 'POST'], 'min' => 'basico', 'roles' => ['admin_general', 'admin_solicitudes', 'empleado_sebana']],
            ['pattern' => '~^/vacaciones/(cancel|reschedule)/\d+$~i', 'methods' => ['POST'], 'min' => 'basico', 'employee_only' => true],

            ['pattern' => '~^/viaticos(?:$|/show$|/pdf$|/archivo$)$~i', 'methods' => ['GET'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']],
            ['pattern' => '~^/viaticos/(create|store)$~i', 'methods' => ['GET', 'POST'], 'min' => 'basico', 'exclude_roles' => ['empleado_sebana']]
        ];
    }

    private static function unauthorizedRedirect(): string
    {
        if (!SecurityHelper::isAuthenticated()) {
            return '/SGA-SEBANA/public/login';
        }

        return self::defaultPanelPath() . '?error=no_autorizado';
    }

    public static function defaultPanelPath(): string
    {
        $base = '/SGA-SEBANA/public';
        $role = self::currentRoleKey();

        switch ($role) {
            case 'admin_rrll':
                return $base . '/casos-rrll';
            case 'admin_solicitudes':
                return $base . '/admin/visit-requests';
            case 'operador':
                return $base . '/afiliados';
            case 'auditor':
                return $base . '/bitacora';
            case 'consulta':
                return $base . '/visit-requests';
            case 'empleado_sebana':
                return $base . '/vacaciones';
            case 'admin_general':
            default:
                return $base . '/home';
        }
    }

    private static function normalizeUri(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');
        if (strlen($uri) > 1) {
            $uri = rtrim($uri, '/');
            if ($uri === '') {
                $uri = '/';
            }
        }
        return $uri;
    }

    private static function normalizeRoleKey(string $roleName): string
    {
        $role = strtolower(trim($roleName));
        switch ($role) {
            case 'administrador general':
                return 'admin_general';
            case 'administrador de rrll':
                return 'admin_rrll';
            case 'administrador de solicitudes':
                return 'admin_solicitudes';
            case 'operador':
                return 'operador';
            case 'consulta':
                return 'consulta';
            case 'empleado de sebana':
            case 'empleado sebana':
                return 'empleado_sebana';
            case 'auditor':
                return 'auditor';
            default:
                return $role;
        }
    }

    private static function matchesAny(array $rules, string $uri, string $method): bool
    {
        foreach ($rules as $rule) {
            if (self::matchRule($rule, $uri, $method)) {
                return true;
            }
        }
        return false;
    }

    private static function matchRule(array $rule, string $uri, string $method): bool
    {
        $methods = $rule['methods'] ?? null;
        if (is_array($methods) && !in_array($method, $methods, true)) {
            return false;
        }

        return (bool) preg_match($rule['pattern'], $uri);
    }
}
