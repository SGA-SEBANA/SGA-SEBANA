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

        // Puestos stats
        $stmt = $this->db->query("SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) as activos,
            SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) as finalizados,
            SUM(CASE WHEN estado = 'suspendido' THEN 1 ELSE 0 END) as suspendidos
            FROM puestos");
        $puestos = $stmt->fetch();

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

        // Afiliados Growth (Last 6 months)
        // Note: Assuming 'fecha_creacion' or similar exists, otherwise use 'fecha_afiliacion'
        // If fecha_afiliacion is not compatible, we might need to adjust or remove this chart.
        // Checking database schema, 'afiliados' has 'fecha_afiliacion'.
        $growth = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd = date('Y-m-t', strtotime("-$i months"));
            $monthLabel = date('M', strtotime("-$i months"));

            $sql = "SELECT COUNT(*) as count FROM afiliados WHERE fecha_creacion BETWEEN :start AND :end";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['start' => $monthStart, 'end' => $monthEnd]);
            $res = $stmt->fetch();
            $growth['labels'][] = $monthLabel;
            $growth['data'][] = (int) $res['count'];
        }

        return [
            'afiliados' => [
                'total' => (int) ($afiliados['total'] ?? 0),
                'activos' => (int) ($afiliados['activos'] ?? 0),
                'inactivos' => (int) ($afiliados['inactivos'] ?? 0),
            ],
            'puestos' => [
                'total' => (int) ($puestos['total'] ?? 0),
                'activos' => (int) ($puestos['activos'] ?? 0),
                'finalizados' => (int) ($puestos['finalizados'] ?? 0),
                'suspendidos' => (int) ($puestos['suspendidos'] ?? 0),
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
            ],
            'charts' => [
                'growth' => $growth
            ]
        ];
    }
}
