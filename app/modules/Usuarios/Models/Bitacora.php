<?php

namespace App\Modules\Usuarios\Models;

use App\Core\ModelBase;
use App\Modules\Usuarios\Helpers\SecurityHelper;

class Bitacora extends ModelBase
{
    protected $table = 'bitacora';

    /**
     * Log an action to the audit table
     * 
     * @param array $data [
     *   'accion' => string (LOGIN, CREATE, UPDATE, DELETE, STATUS_CHANGE, etc.),
     *   'modulo' => string (auth, users, afiliados, etc.),
     *   'entidad' => string (usuario, afiliado, etc.),
     *   'entidad_id' => int|null,
     *   'descripcion' => string,
     *   'datos_anteriores' => array|null,
     *   'datos_nuevos' => array|null,
     *   'resultado' => string (exitoso, fallido, bloqueado)
     * ]
     */
    public function log(array $data): int
    {
        $userId = SecurityHelper::getAuthUserId();

        $record = [
            'usuario_id' => $userId,
            'accion' => $data['accion'] ?? 'UNKNOWN',
            'modulo' => $data['modulo'] ?? 'usuarios',
            'entidad' => $data['entidad'] ?? 'unknown',
            'entidad_id' => $data['entidad_id'] ?? null,
            'descripcion' => $data['descripcion'] ?? null,
            'datos_anteriores' => isset($data['datos_anteriores']) ? json_encode($data['datos_anteriores']) : null,
            'datos_nuevos' => isset($data['datos_nuevos']) ? json_encode($data['datos_nuevos']) : null,
            'ip_address' => SecurityHelper::getClientIp(),
            'user_agent' => substr(SecurityHelper::getUserAgent(), 0, 500),
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? 'CLI',
            'url_accedida' => substr($_SERVER['REQUEST_URI'] ?? '', 0, 500),
            'resultado' => $data['resultado'] ?? 'exitoso',
            'codigo_error' => $data['codigo_error'] ?? null,
            'mensaje_error' => $data['mensaje_error'] ?? null,
        ];

        return $this->create($record);
    }

    /**
     * Log a successful login
     */
    public function logLogin(int $userId, string $username): int
    {
        $record = [
            'usuario_id' => $userId,
            'accion' => 'LOGIN',
            'modulo' => 'auth',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Usuario '{$username}' inició sesión",
            'ip_address' => SecurityHelper::getClientIp(),
            'user_agent' => substr(SecurityHelper::getUserAgent(), 0, 500),
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? 'POST',
            'url_accedida' => $_SERVER['REQUEST_URI'] ?? '/login',
            'resultado' => 'exitoso',
        ];

        return $this->create($record);
    }

    /**
     * Log a failed login attempt
     */
    public function logFailedLogin(?int $userId, string $username, string $reason): int
    {
        $record = [
            'usuario_id' => $userId,
            'accion' => 'LOGIN_FAILED',
            'modulo' => 'auth',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Intento de login fallido para '{$username}': {$reason}",
            'ip_address' => SecurityHelper::getClientIp(),
            'user_agent' => substr(SecurityHelper::getUserAgent(), 0, 500),
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? 'POST',
            'url_accedida' => $_SERVER['REQUEST_URI'] ?? '/login',
            'resultado' => 'fallido',
            'mensaje_error' => $reason,
        ];

        return $this->create($record);
    }

    /**
     * Log a logout action
     */
    public function logLogout(int $userId, string $username): int
    {
        $record = [
            'usuario_id' => $userId,
            'accion' => 'LOGOUT',
            'modulo' => 'auth',
            'entidad' => 'usuario',
            'entidad_id' => $userId,
            'descripcion' => "Usuario '{$username}' cerró sesión",
            'ip_address' => SecurityHelper::getClientIp(),
            'user_agent' => substr(SecurityHelper::getUserAgent(), 0, 500),
            'metodo_http' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'url_accedida' => $_SERVER['REQUEST_URI'] ?? '/logout',
            'resultado' => 'exitoso',
        ];

        return $this->create($record);
    }

    /**
     * Get recent log entries
     */
    public function getRecent(int $limit = 100): array
    {
        $sql = "
            SELECT b.*, u.username, u.nombre_completo as usuario_nombre
            FROM {$this->table} b
            LEFT JOIN usuarios u ON b.usuario_id = u.id
            ORDER BY b.fecha_creacion DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get logs by user ID
     */
    public function getByUser(int $userId, int $limit = 50): array
    {
        $sql = "
            SELECT b.*, u.username, u.nombre_completo as usuario_nombre
            FROM {$this->table} b
            LEFT JOIN usuarios u ON b.usuario_id = u.id
            WHERE b.usuario_id = :userId
            ORDER BY b.fecha_creacion DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get logs by module
     */
    public function getByModule(string $module, int $limit = 50): array
    {
        $sql = "
            SELECT b.*, u.username, u.nombre_completo as usuario_nombre
            FROM {$this->table} b
            LEFT JOIN usuarios u ON b.usuario_id = u.id
            WHERE b.modulo = :module
            ORDER BY b.fecha_creacion DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':module', $module);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Get logs by date range
     */
    public function getByDateRange(string $startDate, string $endDate, int $limit = 500): array
    {
        $sql = "
            SELECT b.*, u.username, u.nombre_completo as usuario_nombre
            FROM {$this->table} b
            LEFT JOIN usuarios u ON b.usuario_id = u.id
            WHERE DATE(b.fecha_creacion) BETWEEN :startDate AND :endDate
            ORDER BY b.fecha_creacion DESC
            LIMIT :limit
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':startDate', $startDate);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Count logs by action type
     */
    public function countByAction(string $action): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE accion = :accion");
        $stmt->execute(['accion' => $action]);
        return (int) $stmt->fetchColumn();
    }
}
