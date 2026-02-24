<?php
namespace App\Modules\Categorias\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class CategoriaModel extends ModelBase {
    
    protected $table = 'categorias';

    public function __construct() {
        parent::__construct();
    }

    // HU-GC-01: Verifica si el nombre ya existe, permitiendo excluir un ID (para ediciones)
    public function existeNombre($nombre, $idExcluir = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE nombre = :nombre";
        $params = [':nombre' => trim($nombre)];

        if ($idExcluir !== null) {
            $sql .= " AND id != :id";
            $params[':id'] = $idExcluir;
        }

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error validando nombre de categoría: " . $e->getMessage());
            return true;
        }
    }

    /**
     * HU-GC-04: Obtener categorías con filtros funcionales (Fijado para PDO)
     */
    public function obtenerTodas($filtros = []) {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
        $params = [];

        if (!empty($filtros['q'])) {
            $sql .= " AND (nombre LIKE :q_nombre OR descripcion LIKE :q_desc)";
            $busqueda = '%' . trim($filtros['q']) . '%';
            $params[':q_nombre'] = $busqueda;
            $params[':q_desc'] = $busqueda;
        }
        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }
        if (!empty($filtros['tipo'])) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

            $sql .= " ORDER BY id ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categorías filtradas: " . $e->getMessage());
            return [];
        }
    }

    // Registro oficial en la base de datos
    public function registrar($nombre, $descripcion, $tipo) {
        $sql = "INSERT INTO {$this->table} (nombre, descripcion, tipo, estado) VALUES (:nombre, :descripcion, :tipo, 'activo')";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre' => trim($nombre),
                ':descripcion' => trim($descripcion),
                ':tipo' => trim($tipo)
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca una categoría específica por su ID
     */
    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar categoría: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Actualiza los datos de una categoría existente
     */
    public function actualizar($id, $nombre, $descripcion, $tipo) {
        $sql = "UPDATE {$this->table} SET nombre = :nombre, descripcion = :descripcion, tipo = :tipo, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nombre' => trim($nombre),
                ':descripcion' => trim($descripcion),
                ':tipo' => trim($tipo),
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error actualizando categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * HU-GC-03: Cambia el estado (Lógica de desactivación con elementos activos)
     */
    public function toggleStatus($id) {
        $categoria = $this->find($id);
        if (!$categoria) return false;

        $nuevoEstado = ($categoria['estado'] === 'activo') ? 'inactivo' : 'activo';

        $sql = "UPDATE {$this->table} SET estado = :estado WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':estado' => $nuevoEstado,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de categoría: " . $e->getMessage());
            return false;
        }
    }

    /**
     * HU-GC-03 (Escenario 1): Verifica si la categoría tiene elementos asociados
     */
    public function tieneAsociaciones($id) {
        // Consultamos en la tabla de afiliados si existe el ID de esta categoría
        $sql = "SELECT COUNT(*) as total FROM afiliados WHERE categoria_id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;
        } catch (PDOException $e) {
            // Si la tabla no existe o falla la consulta, devolvemos false por seguridad
            return false;
        }
    }

    /**
     * HU-GC-03 (Escenario 1): Eliminación física para registros sin asociaciones
     */
    public function eliminar($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Error en eliminación física: " . $e->getMessage());
            return false;
        }
    }
}