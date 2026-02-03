<?php

namespace App\Modules\Usuarios\Models;

use App\Core\ModelBase;

class User extends ModelBase
{
    protected $table = 'usuarios';

    /**
     * Find user by username
     */
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE correo = :email");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }


    public function validateCredentials(string $username, string $password)
    {
        $user = $this->findByUsername($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['contrasena'])) {
            return false;
        }

        return $user;
    }

    /**
     * Check if user is active
     */
    public function isActive(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT estado FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result && $result['estado'] === 'activo';
    }

    /**
     * Check if user is blocked
     */
    public function isBlocked(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT bloqueado FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result && $result['bloqueado'] == 1;
    }

    /**
     * Update last access timestamp
     */
    public function updateLastAccess(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET ultimo_acceso = NOW(), intentos_fallidos = 0 WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(int $id): int
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET intentos_fallidos = intentos_fallidos + 1 WHERE id = :id"
        );
        $stmt->execute(['id' => $id]);

        $stmt = $this->db->prepare("SELECT intentos_fallidos FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        $attempts = $result['intentos_fallidos'] ?? 0;

        if ($attempts >= 5) {
            $this->blockUser($id);
        }

        return $attempts;
    }

    /**
     * Reset failed attempts counter
     */
    public function resetFailedAttempts(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET intentos_fallidos = 0 WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Block a user
     */
    public function blockUser(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET bloqueado = TRUE, fecha_bloqueo = NOW(), estado = 'bloqueado' WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Unblock a user
     */
    public function unblockUser(int $id): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET bloqueado = FALSE, fecha_bloqueo = NULL, intentos_fallidos = 0, estado = 'activo' WHERE id = :id"
        );
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Check if username exists (for validation)
     */
    public function existsUsername(string $username, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = :username";
        $params = ['username' => $username];

        if ($excludeId !== null) {
            $sql .= " AND id != :excludeId";
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if email exists (for validation)
     */
    public function existsEmail(string $email, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE correo = :email";
        $params = ['email' => $email];

        if ($excludeId !== null) {
            $sql .= " AND id != :excludeId";
            $params['excludeId'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get all users with their role names
     */
    public function getAllWithRoles(): array
    {
        $sql = "
            SELECT u.*, r.nombre as rol_nombre, r.nivel_acceso
            FROM {$this->table} u
            LEFT JOIN roles r ON u.rol_id = r.id
            ORDER BY u.fecha_creacion DESC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Get user with role information
     */
    public function findWithRole(int $id): ?array
    {
        $sql = "
            SELECT u.*, r.nombre as rol_nombre, r.nivel_acceso, r.permisos
            FROM {$this->table} u
            LEFT JOIN roles r ON u.rol_id = r.id
            WHERE u.id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * Toggle user status (soft delete)
     */
    public function toggleStatus(int $id): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }

        $newStatus = ($user['estado'] === 'activo') ? 'inactivo' : 'activo';

        $stmt = $this->db->prepare(
            "UPDATE {$this->table} SET estado = :estado WHERE id = :id"
        );

        return $stmt->execute(['estado' => $newStatus, 'id' => $id]);
    }

    /**
     * Get users count by status
     */
    public function countByStatus(string $status): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE estado = :estado");
        $stmt->execute(['estado' => $status]);
        return (int) $stmt->fetchColumn();
    }
}
