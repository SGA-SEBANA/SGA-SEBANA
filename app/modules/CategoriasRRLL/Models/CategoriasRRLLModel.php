<?php

namespace App\Modules\CategoriasRRLL\Models;

use App\Core\ModelBase;
use PDO;

class CategoriasRRLLModel extends ModelBase {
    protected $table = 'categorias';

    public function obtenerTodas($filtros = []) {
        $sql = "SELECT * FROM {$this->table} WHERE tipo = 'caso_rrll'";
        $params = [];

        if (!empty($filtros['q'])) {
            $sql .= " AND nombre LIKE :q";
            $params[':q'] = "%" . $filtros['q'] . "%";
        }

        if (!empty($filtros['fecha'])) {
            $sql .= " AND DATE(fecha_actualizacion) = :fecha";
            $params[':fecha'] = $filtros['fecha'];
        }

        $sql .= " ORDER BY id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function existeNombre($nombre, $idExcluir = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE nombre = :nombre AND tipo = 'caso_rrll'";
        $params = [':nombre' => $nombre];
        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params[':id'] = $idExcluir;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function registrar($nombre, $descripcion) {
        $sql = "INSERT INTO {$this->table} (nombre, descripcion, tipo, estado) VALUES (:nombre, :descripcion, 'caso_rrll', 'activo')";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion]);
    }

    public function actualizar($id, $nombre, $descripcion) {
        $sql = "UPDATE {$this->table} SET nombre = :nombre, descripcion = :descripcion, fecha_actualizacion = NOW() WHERE id = :id AND tipo = 'caso_rrll'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id, ':nombre' => $nombre, ':descripcion' => $descripcion]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id AND tipo = 'caso_rrll'");
        return $stmt->execute([':id' => $id]);
    }

    public function tieneAsociaciones($id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM casos_relaciones_laborales WHERE categoria_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchColumn() > 0;
    }
}