<?php
namespace App\Modules\ReporteDeExclusionDeAfiliado\Models;

use App\Core\ModelBase;
use PDO;

class ReporteDeExclusionDeAfiliado extends ModelBase
{
    protected $table = 'afiliados';

    //Cambiar el limit para permitir mas o menos registros en cada pagina 
    public function getAll($filtros = [], $start = 0, $limit = 10)
    {
        $sql = "SELECT * FROM {$this->table} WHERE estado = 'inactivo'";

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

        // 🔎 Nuevo filtro por nombre
        if (!empty($filtros['nombre'])) {
            $sql .= " AND nombre_completo LIKE :nombre";
            $params['nombre'] = "%".$filtros['nombre']."%";
        }

        $sql .= " ORDER BY fecha_actualizacion DESC
          LIMIT {$start}, {$limit}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($filtros = [])
{
    $sql = "SELECT COUNT(*) FROM {$this->table} WHERE estado = 'inactivo'";
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

    if (!empty($filtros['nombre'])) {
        $sql .= " AND nombre_completo LIKE :nombre";
        $params['nombre'] = "%" . $filtros['nombre'] . "%";
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
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