<?php

namespace App\Modules\Puestos\Models;

use App\Core\ModelBase;
use PDO;

class PuestosModel extends ModelBase
{
    protected $table = 'puestos';

    /**
     * Get all puestos with JOINs, with optional filters
     */
    public function getAll($filtros = [])
    {
        $sql = "SELECT p.*, 
                    a.nombre_completo AS afiliado_nombre,
                    a.cedula AS afiliado_cedula,
                    o.nombre AS oficina_nombre,
                    u.nombre_completo AS asignado_por_nombre
                FROM {$this->table} p
                INNER JOIN afiliados a ON p.afiliado_id = a.id
                LEFT JOIN oficinas o ON p.oficina_id = o.id
                LEFT JOIN usuarios u ON p.asignado_por = u.id
                WHERE 1=1";

        $params = [];

        // Filtro: búsqueda general
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (a.nombre_completo LIKE :b1 OR p.nombre LIKE :b2 OR p.departamento LIKE :b3 OR a.cedula LIKE :b4)";
            $termino = "%" . $filtros['busqueda'] . "%";
            $params['b1'] = $termino;
            $params['b2'] = $termino;
            $params['b3'] = $termino;
            $params['b4'] = $termino;
        }

        // Filtro: estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        // Filtro: afiliado específico
        if (!empty($filtros['afiliado_id'])) {
            $sql .= " AND p.afiliado_id = :afiliado_id";
            $params['afiliado_id'] = $filtros['afiliado_id'];
        }

        $sql .= " ORDER BY p.fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single puesto by ID with JOINed names
     */
    public function getById($id)
    {
        $sql = "SELECT p.*, 
                    a.nombre_completo AS afiliado_nombre,
                    a.cedula AS afiliado_cedula,
                    o.nombre AS oficina_nombre,
                    u.nombre_completo AS asignado_por_nombre
                FROM {$this->table} p
                INNER JOIN afiliados a ON p.afiliado_id = a.id
                LEFT JOIN oficinas o ON p.oficina_id = o.id
                LEFT JOIN usuarios u ON p.asignado_por = u.id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all puestos for a specific afiliado (history)
     */
    public function getByAfiliado($afiliadoId)
    {
        $sql = "SELECT p.*, 
                    a.nombre_completo AS afiliado_nombre,
                    a.cedula AS afiliado_cedula,
                    o.nombre AS oficina_nombre,
                    u.nombre_completo AS asignado_por_nombre
                FROM {$this->table} p
                INNER JOIN afiliados a ON p.afiliado_id = a.id
                LEFT JOIN oficinas o ON p.oficina_id = o.id
                LEFT JOIN usuarios u ON p.asignado_por = u.id
                WHERE p.afiliado_id = :afiliado_id
                ORDER BY p.fecha_asignacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['afiliado_id' => $afiliadoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new puesto. If estado is 'activo', deactivate previous and sync.
     */
    public function createPuesto($data)
    {
        // If new puesto is 'activo', deactivate existing active ones for same afiliado
        if (($data['estado'] ?? 'activo') === 'activo') {
            $this->deactivateOtherPuestos($data['afiliado_id']);
        }

        $sql = "INSERT INTO {$this->table} (
                    afiliado_id, nombre, descripcion, departamento, oficina_id,
                    fecha_asignacion, fecha_remocion, motivo_remocion, salario_base,
                    tipo_contrato, jornada, estado, asignado_por, observaciones
                ) VALUES (
                    :afiliado_id, :nombre, :descripcion, :departamento, :oficina_id,
                    :fecha_asignacion, :fecha_remocion, :motivo_remocion, :salario_base,
                    :tipo_contrato, :jornada, :estado, :asignado_por, :observaciones
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'afiliado_id' => $data['afiliado_id'],
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?: null,
            'departamento' => $data['departamento'] ?: null,
            'oficina_id' => !empty($data['oficina_id']) ? $data['oficina_id'] : null,
            'fecha_asignacion' => $data['fecha_asignacion'],
            'fecha_remocion' => !empty($data['fecha_remocion']) ? $data['fecha_remocion'] : null,
            'motivo_remocion' => $data['motivo_remocion'] ?: null,
            'salario_base' => !empty($data['salario_base']) ? $data['salario_base'] : null,
            'tipo_contrato' => $data['tipo_contrato'] ?? 'indefinido',
            'jornada' => $data['jornada'] ?? 'completa',
            'estado' => $data['estado'] ?? 'activo',
            'asignado_por' => !empty($data['asignado_por']) ? $data['asignado_por'] : null,
            'observaciones' => $data['observaciones'] ?: null,
        ]);

        $newId = $this->db->lastInsertId();

        // Sync puesto_actual in afiliados
        $this->syncPuestoActual($data['afiliado_id']);

        return $newId;
    }

    /**
     * Update an existing puesto. Handles sync logic.
     */
    public function updatePuesto($id, $data)
    {
        // Get current puesto to know the afiliado
        $current = $this->find($id);
        if (!$current)
            return false;

        $afiliadoId = $current['afiliado_id'];

        // If estado changed to 'activo', deactivate others
        if (($data['estado'] ?? '') === 'activo') {
            $this->deactivateOtherPuestos($afiliadoId, $id);
        }

        $sql = "UPDATE {$this->table} SET
                    nombre = :nombre,
                    descripcion = :descripcion,
                    departamento = :departamento,
                    oficina_id = :oficina_id,
                    fecha_asignacion = :fecha_asignacion,
                    fecha_remocion = :fecha_remocion,
                    motivo_remocion = :motivo_remocion,
                    salario_base = :salario_base,
                    tipo_contrato = :tipo_contrato,
                    jornada = :jornada,
                    estado = :estado,
                    asignado_por = :asignado_por,
                    observaciones = :observaciones
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?: null,
            'departamento' => $data['departamento'] ?: null,
            'oficina_id' => !empty($data['oficina_id']) ? $data['oficina_id'] : null,
            'fecha_asignacion' => $data['fecha_asignacion'],
            'fecha_remocion' => !empty($data['fecha_remocion']) ? $data['fecha_remocion'] : null,
            'motivo_remocion' => $data['motivo_remocion'] ?: null,
            'salario_base' => !empty($data['salario_base']) ? $data['salario_base'] : null,
            'tipo_contrato' => $data['tipo_contrato'] ?? 'indefinido',
            'jornada' => $data['jornada'] ?? 'completa',
            'estado' => $data['estado'] ?? 'activo',
            'asignado_por' => !empty($data['asignado_por']) ? $data['asignado_por'] : null,
            'observaciones' => $data['observaciones'] ?: null,
        ]);

        // Sync puesto_actual in afiliados
        $this->syncPuestoActual($afiliadoId);

        return $result;
    }

    /**
     * Delete a puesto and recalculate puesto_actual
     */
    public function deletePuesto($id)
    {
        $current = $this->find($id);
        if (!$current)
            return false;

        $afiliadoId = $current['afiliado_id'];

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        $result = $stmt->execute(['id' => $id]);

        // Recalculate puesto_actual
        $this->syncPuestoActual($afiliadoId);

        return $result;
    }

    /**
     * Toggle puesto estado and handle sync
     */
    public function toggleEstado($id, $nuevoEstado)
    {
        $current = $this->find($id);
        if (!$current)
            return false;

        $afiliadoId = $current['afiliado_id'];

        // If activating, deactivate others first
        if ($nuevoEstado === 'activo') {
            $this->deactivateOtherPuestos($afiliadoId, $id);
        }

        $sql = "UPDATE {$this->table} SET estado = :estado";
        $params = ['estado' => $nuevoEstado, 'id' => $id];

        // If finalizing, set fecha_remocion to today
        if ($nuevoEstado === 'finalizado') {
            $sql .= ", fecha_remocion = CURDATE()";
        } elseif ($nuevoEstado === 'activo') {
            $sql .= ", fecha_remocion = NULL";
        }

        $sql .= " WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);

        // Sync puesto_actual
        $this->syncPuestoActual($afiliadoId);

        return $result;
    }

    /**
     * Deactivate all active puestos for an afiliado (except excludeId)
     */
    private function deactivateOtherPuestos($afiliadoId, $excludeId = null)
    {
        $sql = "UPDATE {$this->table} SET estado = 'finalizado', fecha_remocion = CURDATE()
                WHERE afiliado_id = :afiliado_id AND estado = 'activo'";
        $params = ['afiliado_id' => $afiliadoId];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    /**
     * Sync afiliados.puesto_actual with the current active puesto
     */
    public function syncPuestoActual($afiliadoId)
    {
        // Find the active puesto for this afiliado
        $stmt = $this->db->prepare(
            "SELECT nombre FROM {$this->table} 
             WHERE afiliado_id = :afiliado_id AND estado = 'activo' 
             ORDER BY fecha_asignacion DESC LIMIT 1"
        );
        $stmt->execute(['afiliado_id' => $afiliadoId]);
        $activePuesto = $stmt->fetch(PDO::FETCH_ASSOC);

        $puestoActual = $activePuesto ? $activePuesto['nombre'] : '';

        // Update afiliados table
        $update = $this->db->prepare("UPDATE afiliados SET puesto_actual = :puesto WHERE id = :id");
        $update->execute(['puesto' => $puestoActual, 'id' => $afiliadoId]);
    }

    /**
     * Get active afiliados for dropdown
     */
    public function getAfiliados()
    {
        $sql = "SELECT id, nombre_completo, cedula FROM afiliados WHERE estado = 'activo' ORDER BY nombre_completo ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active oficinas for dropdown
     */
    public function getOficinas()
    {
        $sql = "SELECT id, nombre, codigo FROM oficinas WHERE activo = 1 ORDER BY nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active usuarios for "asignado_por" dropdown
     */
    public function getUsuarios()
    {
        $sql = "SELECT id, nombre_completo, username FROM usuarios WHERE estado = 'activo' ORDER BY nombre_completo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get statistics for the report view
     */
    public function getEstadisticas()
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
                    SUM(CASE WHEN estado = 'suspendido' THEN 1 ELSE 0 END) as suspendidos
                FROM {$this->table}";
        $stmt = $this->db->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get afiliado info by ID
     */
    public function getAfiliadoById($id)
    {
        $sql = "SELECT id, nombre_completo, cedula FROM afiliados WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
