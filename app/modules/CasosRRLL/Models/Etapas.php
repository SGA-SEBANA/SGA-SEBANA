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
        $nuevoOrden = isset($datos['orden']) && (int) $datos['orden'] > 0
            ? (int) $datos['orden']
            : $this->obtenerSiguienteOrden($datos['caso_id']);

        if ($this->existeOrdenEnCaso($datos['caso_id'], $nuevoOrden)) {
            return false;
        }

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
        return $this->actualizarEstadoConFecha($id, $nuevoEstado);
    }

    public function actualizarEstadoConFecha($id, $nuevoEstado, ?string $fechaReal = null): bool
    {
        $estados_validos = ['pendiente', 'en_progreso', 'finalizado', 'bloqueado', 'cancelado'];
        if (!in_array($nuevoEstado, $estados_validos, true)) {
            return false;
        }

        $sql = "UPDATE {$this->table}
                SET estado = :estado_set,
                    fecha_fin = CASE
                        WHEN :estado_eval = 'finalizado' AND :fecha_fin_eval IS NOT NULL AND TRIM(:fecha_fin_eval) <> '' THEN :fecha_fin_set
                        WHEN :estado_eval2 = 'finalizado' AND fecha_fin IS NULL THEN CURDATE()
                        WHEN :estado_eval3 <> 'finalizado' THEN NULL
                        ELSE fecha_fin
                    END,
                    fecha_actualizacion = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'estado_set' => $nuevoEstado,
            'estado_eval' => $nuevoEstado,
            'estado_eval2' => $nuevoEstado,
            'estado_eval3' => $nuevoEstado,
            'fecha_fin_eval' => $fechaReal,
            'fecha_fin_set' => $fechaReal,
            'id' => $id
        ]);
    }

    /**
     * Eliminar etapa
     */
    public function delete($id)
    {
        // Soft delete: cancelar etapa
        $sql = "UPDATE {$this->table}
                SET estado = 'cancelado',
                    fecha_actualizacion = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
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

    public function obtenerSiguienteOrden($casoId): int
    {
        $sql = "SELECT MAX(orden) as max_orden FROM {$this->table} WHERE caso_id = :caso_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ((int) ($result['max_orden'] ?? 0)) + 1;
    }

    public function existeOrdenEnCaso($casoId, $orden, $idExcluir = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE caso_id = :caso_id AND orden = :orden";
        $params = [
            'caso_id' => $casoId,
            'orden' => $orden
        ];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function existeEtapaPreviaSinCompletar($casoId, $ordenActual, $idActual = null): bool
    {
        $sql = "SELECT COUNT(*)
                FROM {$this->table}
                WHERE caso_id = :caso_id
                  AND orden < :orden
                  AND estado NOT IN ('finalizado', 'cancelado')";
        $params = [
            'caso_id' => $casoId,
            'orden' => $ordenActual
        ];

        if ($idActual) {
            $sql .= " AND id != :id";
            $params['id'] = $idActual;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function obtenerDocumentosEtapa($etapaId): array
    {
        $sql = "SELECT documentos_generados FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $etapaId]);
        $raw = (string) ($stmt->fetchColumn() ?? '');
        return $this->decodeDocumentList($raw);
    }

    public function guardarDocumentosEtapa($etapaId, array $documentos): bool
    {
        $sql = "UPDATE {$this->table}
                SET documentos_generados = :documentos,
                    fecha_actualizacion = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'documentos' => json_encode(array_values($documentos), JSON_UNESCAPED_UNICODE),
            'id' => $etapaId
        ]);
    }

    public function agregarDocumentoEtapa($etapaId, array $documento): bool
    {
        $docs = $this->obtenerDocumentosEtapa($etapaId);
        $docs[] = $documento;
        return $this->guardarDocumentosEtapa($etapaId, $docs);
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

    private function decodeDocumentList(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, static fn($item) => is_array($item)));
        }

        $legacyItems = array_filter(array_map('trim', explode(',', $raw)));
        $result = [];
        foreach ($legacyItems as $legacy) {
            $result[] = [
                'nombre_original' => $legacy,
                'ruta' => null,
                'fecha' => null,
                'tamano' => null,
                'mime' => null
            ];
        }
        return $result;
    }
}
