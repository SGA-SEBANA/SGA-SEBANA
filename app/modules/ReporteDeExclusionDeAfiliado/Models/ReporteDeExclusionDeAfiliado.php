<?php
namespace App\Modules\ReporteDeExclusionDeAfiliado\Models;

use App\Core\ModelBase;
use PDO;

class ReporteDeExclusionDeAfiliado extends ModelBase
{
    protected $table = 'afiliados';

    public function getAll($filtros = [])
    {
        // Mostrar registros con fecha_baja o estado inactivo
        $sql = "SELECT * FROM {$this->table} WHERE (fecha_baja IS NOT NULL OR estado = 'inactivo')";
        $params = [];

        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $sql .= " AND fecha_baja BETWEEN :inicio AND :fin";
            $params['inicio'] = $filtros['fecha_inicio'];
            $params['fin'] = $filtros['fecha_fin'];
        }

        if (!empty($filtros['tipo_baja'])) {
            $sql .= " AND tipo_baja = :tipo_baja";
            $params['tipo_baja'] = $filtros['tipo_baja'];
        }

        if (!empty($filtros['estado'])) {
            $sql .= " AND estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        $sql .= " ORDER BY fecha_actualizacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE id = :id 
                AND (fecha_baja IS NOT NULL OR estado = 'inactivo') 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
