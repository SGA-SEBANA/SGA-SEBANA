<?php

namespace App\Modules\CasosRRLL\Models;

use App\Core\ModelBase;
use PDO;

class Etapas extends ModelBase
{
    protected $table = 'etapas_casos';

    /**
     * Obtener una etapa por ID
     */
    public function getById($id)
    {
        $sql = "SELECT e.*, 
                       c.numero_expediente,
                       c.titulo as caso_titulo,
                       u.nombre_completo as responsable_nombre
                FROM {$this->table} e
                LEFT JOIN casos_relaciones_laborales c ON e.caso_id = c.id
                LEFT JOIN usuarios u ON e.responsable_id = u.id
                WHERE e.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todas las etapas de un caso
     */
    public function getEtapasCaso($casoId, $conFiltros = false)
    {
        $sql = "SELECT e.*, u.nombre_completo as responsable_nombre
                FROM {$this->table} e
                LEFT JOIN usuarios u ON e.responsable_id = u.id
                WHERE e.caso_id = :caso_id
                ORDER BY e.orden ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verificar si existe nombre de etapa en ese caso
     */
    public function existeNombreEnCaso($nombre, $casoId, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE caso_id = :caso_id AND nombre = :nombre";
        $params = ['caso_id' => $casoId, 'nombre' => $nombre];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Crear nueva etapa
     */
    public function create($datos)
    {
        // Obtener el máximo orden para ese caso
        $sqlMaxOrden = "SELECT MAX(orden) as max_orden FROM {$this->table} WHERE caso_id = :caso_id";
        $stmtOrden = $this->db->prepare($sqlMaxOrden);
        $stmtOrden->execute(['caso_id' => $datos['caso_id']]);
        $resultOrden = $stmtOrden->fetch(PDO::FETCH_ASSOC);
        $nuevoOrden = ($resultOrden['max_orden'] ?? 0) + 1;

        $sql = "INSERT INTO {$this->table} 
                (caso_id, nombre, descripcion, orden, estado, fecha_inicio, 
                 fecha_estimada_fin, responsable_id, observaciones)
                VALUES 
                (:caso_id, :nombre, :descripcion, :orden, :estado, :fecha_inicio,
                 :fecha_estimada_fin, :responsable_id, :observaciones)";

        $params = [
            'caso_id' => $datos['caso_id'],
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'orden' => $nuevoOrden,
            'estado' => $datos['estado'] ?? 'pendiente',
            'fecha_inicio' => $datos['fecha_inicio'] ?? date('Y-m-d'),
            'fecha_estimada_fin' => $datos['fecha_estimada_fin'] ?? null,
            'responsable_id' => $datos['responsable_id'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($params)) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualizar etapa
     */
    public function update($id, $datos)
    {
        $sql = "UPDATE {$this->table} SET 
                nombre = :nombre,
                descripcion = :descripcion,
                estado = :estado,
                fecha_inicio = :fecha_inicio,
                fecha_fin = :fecha_fin,
                fecha_estimada_fin = :fecha_estimada_fin,
                responsable_id = :responsable_id,
                resultado = :resultado,
                documentos_generados = :documentos_generados,
                observaciones = :observaciones
                WHERE id = :id";

        $params = [
            'id' => $id,
            'nombre' => $datos['nombre'],
            'descripcion' => $datos['descripcion'] ?? null,
            'estado' => $datos['estado'] ?? 'pendiente',
            'fecha_inicio' => $datos['fecha_inicio'] ?? null,
            'fecha_fin' => $datos['fecha_fin'] ?? null,
            'fecha_estimada_fin' => $datos['fecha_estimada_fin'] ?? null,
            'responsable_id' => $datos['responsable_id'] ?? null,
            'resultado' => $datos['resultado'] ?? null,
            'documentos_generados' => $datos['documentos_generados'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cambiar estado de etapa
     */
    public function cambiarEstado($id, $nuevoEstado)
    {
        $estados_validos = ['pendiente', 'en_progreso', 'finalizado', 'bloqueado', 'cancelado'];

        if (!in_array($nuevoEstado, $estados_validos)) {
            return false;
        }

        // Si el nuevo estado es finalizado, establecer fecha_fin
        $params = ['estado' => $nuevoEstado, 'id' => $id];
        
        if ($nuevoEstado === 'finalizado') {
            $sql = "UPDATE {$this->table} SET 
                    estado = :estado,
                    fecha_fin = NOW(),
                    fecha_actualizacion = NOW()
                    WHERE id = :id";
        } else {
            $sql = "UPDATE {$this->table} SET 
                    estado = :estado,
                    fecha_actualizacion = NOW()
                    WHERE id = :id";
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Eliminar etapa
     */
    public function delete($id)
    {
        // Obtener el caso_id y orden de la etapa a eliminar
        $etapa = $this->getById($id);
        if (!$etapa) return false;

        // Eliminar la etapa
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $resultado = $stmt->execute(['id' => $id]);

        // Reordenar las etapas restantes del caso
        if ($resultado) {
            $this->reordenarEtapas($etapa['caso_id']);
        }

        return $resultado;
    }

    /**
     * Reordenar etapas de un caso después de eliminación
     */
    public function reordenarEtapas($casoId)
    {
        $sql = "SELECT id FROM {$this->table} 
                WHERE caso_id = :caso_id 
                ORDER BY orden ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        $etapas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($etapas as $index => $etapa) {
            $sql = "UPDATE {$this->table} SET orden = :orden WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['orden' => $index + 1, 'id' => $etapa['id']]);
        }

        return true;
    }

    /**
     * Obtener historial de etapas (cambios de estado)
     */
    public function getHistorialEtapas($casoId)
    {
        $sql = "SELECT *, 
                DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion_format,
                DATE_FORMAT(fecha_actualizacion, '%d/%m/%Y %H:%i') as fecha_actualizacion_format
                FROM {$this->table}
                WHERE caso_id = :caso_id
                ORDER BY fecha_actualizacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener usuarios para asignar como responsables
     */
    public function getUsuarios()
    {
        $sql = "SELECT id, nombre_completo, username 
                FROM usuarios 
                WHERE estado = 'activo' 
                ORDER BY nombre_completo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener progreso de un caso (etapas completadas vs total)
     */
    public function getProgresoCaso($casoId)
    {
        $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'finalizado' THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN estado = 'en_progreso' THEN 1 ELSE 0 END) as en_progreso,
                SUM(CASE WHEN estado = 'bloqueado' THEN 1 ELSE 0 END) as bloqueadas
                FROM {$this->table}
                WHERE caso_id = :caso_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener próxima etapa sin iniciar
     */
    public function getProximaEtapa($casoId)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE caso_id = :caso_id AND estado = 'pendiente'
                ORDER BY orden ASC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calcular duración de una etapa
     */
    public function calcularDuracion($id)
    {
        $etapa = $this->getById($id);
        if (!$etapa) return 0;

        $fecha_inicio = new \DateTime($etapa['fecha_inicio']);
        $fecha_fin = $etapa['fecha_fin'] ? new \DateTime($etapa['fecha_fin']) : new \DateTime();

        $interval = $fecha_inicio->diff($fecha_fin);
        return $interval->days;
    }
}
