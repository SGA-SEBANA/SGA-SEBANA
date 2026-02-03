<?php

namespace App\Modules\Home\Models;

use App\Core\ModelBase;

/**
 * HomeModel - Handles data retrieval for the Dashboard
 */
class HomeModel extends ModelBase
{
    /**
     * Get summary statistics for the dashboard
     */
    public function getSummaryStats(): array
    {
        // Afiliados stats
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) as inactivos
            FROM afiliados");
        $afiliados = $stmt->fetch();

        // Usuarios stats
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos
            FROM usuarios");
        $usuarios = $stmt->fetch();

        // Junta Directiva stats (Vigentes)
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM junta_directiva WHERE estado = 'vigente'");
        $junta = $stmt->fetch();

        // Bitacora - Total logs today
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM bitacora WHERE DATE(fecha_creacion) = CURRENT_DATE");
        $logs_hoy = $stmt->fetch();

        return [
            'afiliados' => [
                'total' => (int) ($afiliados['total'] ?? 0),
                'activos' => (int) ($afiliados['activos'] ?? 0),
                'inactivos' => (int) ($afiliados['inactivos'] ?? 0),
            ],
            'usuarios' => [
                'total' => (int) ($usuarios['total'] ?? 0),
                'activos' => (int) ($usuarios['activos'] ?? 0),
            ],
            'junta' => [
                'total' => (int) ($junta['total'] ?? 0),
            ],
            'logs_hoy' => [
                'total' => (int) ($logs_hoy['total'] ?? 0),
            ]
        ];
    }
}
