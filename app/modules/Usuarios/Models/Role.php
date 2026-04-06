<?php

namespace App\Modules\Usuarios\Models;

use App\Core\ModelBase;

class Role extends ModelBase
{
    protected $table = 'roles';

    private function ensureBaseRoles(): void
    {
        $this->ensureRole(
            'Empleado de SEBANA',
            'Empleado interno de SEBANA (no afiliado BN) con acceso al modulo de vacaciones.',
            'basico'
        );
    }

    private function ensureRole(string $nombre, string $descripcion, string $nivel): void
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE nombre = :nombre LIMIT 1");
        $stmt->execute(['nombre' => $nombre]);
        $row = $stmt->fetch();
        if ($row) {
            return;
        }

        $insert = $this->db->prepare(
            "INSERT INTO {$this->table} (nombre, descripcion, nivel_acceso, activo) VALUES (:nombre, :descripcion, :nivel, 1)"
        );
        $insert->execute([
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'nivel' => $nivel
        ]);
    }

    /**
     * Get active roles for dropdown
     */
    public function getActive(): array
    {
        $this->ensureBaseRoles();
        $stmt = $this->db->query(
            "SELECT id, nombre, descripcion, nivel_acceso FROM {$this->table} WHERE activo = TRUE ORDER BY nombre ASC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Get role by name
     */
    public function findByName(string $nombre): ?array
    {
        $this->ensureBaseRoles();
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE nombre = :nombre");
        $stmt->execute(['nombre' => $nombre]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Get roles count
     */
    public function countActive(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE activo = TRUE");
        return (int) $stmt->fetchColumn();
    }

    /**
     * Get roles with user count
     */
    public function getAllWithUserCount(): array
    {
        $this->ensureBaseRoles();
        $sql = "
            SELECT r.*, 
                   (SELECT COUNT(*) FROM usuarios u WHERE u.rol_id = r.id) as user_count
            FROM {$this->table} r
            ORDER BY r.nombre ASC
        ";

        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
