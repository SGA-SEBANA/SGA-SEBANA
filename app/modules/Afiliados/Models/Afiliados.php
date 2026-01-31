<?php
namespace App\Modules\Afiliados\Models;

use App\Core\ModelBase;
use PDO;

class Afiliados extends ModelBase {
    
    protected $table = 'afiliados';

    public function existeCedula($cedula, $idExcluir = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE cedula = :cedula";
        $params = ['cedula' => $cedula];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                nombre_completo = :nombre_completo,
                cedula = :cedula,
                numero_empleado = :numero_empleado,
                genero = :genero,
                fecha_nacimiento = :fecha_nacimiento,
                oficina_nombre = :oficina_nombre,
                oficina_numero = :oficina_numero,
                categoria = :categoria,
                email_institucional = :email_institucional,
                celular_personal = :celular_personal,
                updated_at = NOW()
                WHERE id = :id";
        
        $data['id'] = $id;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * NUEVO: Cambiar el estado de un afiliado (Toggle)
     * Si es 'activo' pasa a 'inactivo' y viceversa.
     */
    public function toggleStatus($id) {
        // 1. Obtener estado actual
        $current = $this->getById($id);
        if (!$current) return false;

        // 2. Calcular nuevo estado
        $nuevoEstado = ($current['estado'] === 'activo') ? 'inactivo' : 'activo';

        // 3. Actualizar
        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = :estado WHERE id = :id");
        return $stmt->execute(['estado' => $nuevoEstado, 'id' => $id]);
    }
}