<?php

namespace App\Modules\CasosRRLL\Models;

use App\Core\ModelBase;
use PDO;

class CasosRRLL extends ModelBase
{
    protected $table = 'casos_relaciones_laborales';
    private array $enumCache = [];

    private function getEnumValues(string $column): array
    {
        if (isset($this->enumCache[$column])) {
            return $this->enumCache[$column];
        }

        try {
            $sql = "SELECT COLUMN_TYPE
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA = DATABASE()
                      AND TABLE_NAME = :table
                      AND COLUMN_NAME = :column
                    LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'table' => $this->table,
                'column' => $column
            ]);
            $columnType = (string) ($stmt->fetchColumn() ?: '');
            if (preg_match("/^enum\\((.*)\\)$/i", $columnType, $matches)) {
                $raw = $matches[1];
                $values = str_getcsv($raw, ',', "'");
                $values = array_values(array_filter(array_map('trim', $values), static fn($v) => $v !== ''));
                return $this->enumCache[$column] = $values;
            }
        } catch (\Throwable $e) {
            // Fallback por compatibilidad entre entornos.
        }

        return $this->enumCache[$column] = [];
    }

    private function normalizeToken(string $value): string
    {
        $value = strtolower(trim($value));
        $value = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $value);
        $value = str_replace([' ', '-'], '_', $value);
        return preg_replace('/_+/', '_', $value) ?: '';
    }

    private function pickEnumValue(array $enumValues, array $candidates): ?string
    {
        if (empty($enumValues)) {
            return $candidates[0] ?? null;
        }

        $normalizedMap = [];
        foreach ($enumValues as $value) {
            $normalizedMap[$this->normalizeToken((string) $value)] = (string) $value;
        }

        foreach ($candidates as $candidate) {
            $key = $this->normalizeToken((string) $candidate);
            if (isset($normalizedMap[$key])) {
                return $normalizedMap[$key];
            }
        }

        return null;
    }

    private function getCaseStateCandidates(string $logicalState): array
    {
        $state = $this->normalizeToken($logicalState);
        $candidatesByState = [
            'activo' => ['activo', 'abierto'],
            'en_progreso' => ['en_progreso', 'en_tramite', 'en_proceso'],
            'suspendido' => ['suspendido', 'suspendida'],
            'cerrado' => ['cerrado', 'finalizado', 'finalizada'],
            'archivado' => ['archivado', 'anulado', 'inactivo']
        ];

        if (!isset($candidatesByState[$state])) {
            return [];
        }

        return $candidatesByState[$state];
    }

    private function mapCaseStateForDb(string $logicalState): ?string
    {
        $enumValues = $this->getEnumValues('estado');
        $candidates = $this->getCaseStateCandidates($logicalState);
        if (empty($candidates)) {
            return null;
        }
        return $this->pickEnumValue($enumValues, $candidates);
    }

    /**
     * Verificar si existe un expediente
     */
    public function existeExpediente($numero_expediente, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE numero_expediente = :expediente";
        $params = ['expediente' => $numero_expediente];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener todos los casos con filtros
     */
    public function getAll($filtros = [])
    {
        $sql = "SELECT c.*, 
                       a.nombre_completo as afiliado_nombre,
                       cat.nombre as categoria_nombre,
                       u_creador.nombre_completo as creado_por_nombre,
                       u_responsable.nombre_completo as responsable_nombre,
                       COALESCE(
                           (
                               SELECT e_act.nombre
                               FROM etapas_casos e_act
                               WHERE e_act.caso_id = c.id
                                 AND e_act.estado IN ('pendiente', 'en_progreso', 'bloqueado')
                               ORDER BY e_act.orden ASC
                               LIMIT 1
                           ),
                           (
                               SELECT e_last.nombre
                               FROM etapas_casos e_last
                               WHERE e_last.caso_id = c.id
                               ORDER BY e_last.orden DESC
                               LIMIT 1
                           )
                       ) AS etapa_actual_nombre,
                       (
                           SELECT e_est.estado
                           FROM etapas_casos e_est
                           WHERE e_est.caso_id = c.id
                             AND e_est.estado IN ('pendiente', 'en_progreso', 'bloqueado')
                           ORDER BY e_est.orden ASC
                           LIMIT 1
                       ) AS etapa_actual_estado,
                       (
                           SELECT e_est.fecha_estimada_fin
                           FROM etapas_casos e_est
                           WHERE e_est.caso_id = c.id
                             AND e_est.estado IN ('pendiente', 'en_progreso', 'bloqueado')
                           ORDER BY e_est.orden ASC
                           LIMIT 1
                       ) AS etapa_actual_fecha_estimada,
                       (
                           SELECT COUNT(*)
                           FROM etapas_casos e_total
                           WHERE e_total.caso_id = c.id
                       ) AS total_etapas,
                       (
                           SELECT COUNT(*)
                           FROM etapas_casos e_fin
                           WHERE e_fin.caso_id = c.id
                             AND e_fin.estado = 'finalizado'
                       ) AS etapas_finalizadas,
                       (
                           CASE
                               WHEN c.documentos_adjuntos IS NULL OR TRIM(c.documentos_adjuntos) = '' THEN 0
                               WHEN JSON_VALID(c.documentos_adjuntos) THEN JSON_LENGTH(c.documentos_adjuntos)
                               ELSE 1
                           END
                       ) + (
                           SELECT COALESCE(SUM(
                               CASE
                                   WHEN e_doc.documentos_generados IS NULL OR TRIM(e_doc.documentos_generados) = '' THEN 0
                                   WHEN JSON_VALID(e_doc.documentos_generados) THEN JSON_LENGTH(e_doc.documentos_generados)
                                   ELSE 1
                               END
                           ), 0)
                           FROM etapas_casos e_doc
                           WHERE e_doc.caso_id = c.id
                       ) AS total_documentos,
                       (
                           SELECT MAX(b.fecha_creacion)
                           FROM bitacora b
                           WHERE b.modulo = 'casos_rrll'
                             AND (
                                 (b.entidad = 'caso' AND b.entidad_id = c.id)
                                 OR (
                                     b.entidad = 'etapa'
                                     AND b.entidad_id IN (SELECT e_bit.id FROM etapas_casos e_bit WHERE e_bit.caso_id = c.id)
                                 )
                                 OR (b.entidad = 'documento_rrll' AND b.entidad_id = c.id)
                             )
                       ) AS ultima_actuacion
                FROM {$this->table} c
                LEFT JOIN afiliados a ON c.afiliado_id = a.id
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios u_creador ON c.creado_por = u_creador.id
                LEFT JOIN usuarios u_responsable ON c.responsable_actual = u_responsable.id
                WHERE 1=1";

        $params = [];

        // Búsqueda general
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (c.numero_expediente LIKE :busqueda OR c.titulo LIKE :busqueda2 OR a.nombre_completo LIKE :busqueda3)";
            $termino = "%" . $filtros['busqueda'] . "%";
            $params['busqueda'] = $termino;
            $params['busqueda2'] = $termino;
            $params['busqueda3'] = $termino;
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        // Filtro por categoría
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND c.categoria_id = :categoria_id";
            $params['categoria_id'] = $filtros['categoria_id'];
        }

        // Filtro por prioridad
        if (!empty($filtros['prioridad'])) {
            $sql .= " AND c.prioridad = :prioridad";
            $params['prioridad'] = $filtros['prioridad'];
        }

        // Filtro por etapa (nombre contiene texto, p.ej. investigacion)
        if (!empty($filtros['etapa_nombre'])) {
            $sql .= " AND EXISTS (
                        SELECT 1
                        FROM etapas_casos e
                        WHERE e.caso_id = c.id
                          AND e.nombre LIKE :etapa_nombre
                     )";
            $params['etapa_nombre'] = '%' . $filtros['etapa_nombre'] . '%';
        }

        // Filtro por estado de etapa
        if (!empty($filtros['etapa_estado'])) {
            $sql .= " AND EXISTS (
                        SELECT 1
                        FROM etapas_casos e_estado
                        WHERE e_estado.caso_id = c.id
                          AND e_estado.estado = :etapa_estado
                     )";
            $params['etapa_estado'] = $filtros['etapa_estado'];
        }

        // Filtro por afiliado
        if (!empty($filtros['afiliado_id'])) {
            $sql .= " AND c.afiliado_id = :afiliado_id";
            $params['afiliado_id'] = $filtros['afiliado_id'];
        }

        // Filtro por responsable
        if (!empty($filtros['responsable_id'])) {
            $sql .= " AND c.responsable_actual = :responsable_id";
            $params['responsable_id'] = $filtros['responsable_id'];
        }

        // Filtro por documentos
        if (($filtros['con_documentos'] ?? '') === 'si') {
            $sql .= " AND (
                        (c.documentos_adjuntos IS NOT NULL AND TRIM(c.documentos_adjuntos) <> '')
                        OR EXISTS (
                            SELECT 1 FROM etapas_casos e_docs
                            WHERE e_docs.caso_id = c.id
                              AND e_docs.documentos_generados IS NOT NULL
                              AND TRIM(e_docs.documentos_generados) <> ''
                        )
                     )";
        }
        if (($filtros['con_documentos'] ?? '') === 'no') {
            $sql .= " AND (
                        (c.documentos_adjuntos IS NULL OR TRIM(c.documentos_adjuntos) = '')
                        AND NOT EXISTS (
                            SELECT 1 FROM etapas_casos e_docs
                            WHERE e_docs.caso_id = c.id
                              AND e_docs.documentos_generados IS NOT NULL
                              AND TRIM(e_docs.documentos_generados) <> ''
                        )
                     )";
        }

        // Filtro por atraso (etapa activa vencida)
        if (($filtros['atraso'] ?? '') === 'vencido') {
            $sql .= " AND EXISTS (
                        SELECT 1
                        FROM etapas_casos e_due
                        WHERE e_due.caso_id = c.id
                          AND e_due.estado IN ('pendiente', 'en_progreso', 'bloqueado')
                          AND e_due.fecha_estimada_fin IS NOT NULL
                          AND e_due.fecha_estimada_fin < CURDATE()
                     )";
        }
        if (($filtros['atraso'] ?? '') === 'al_dia') {
            $sql .= " AND NOT EXISTS (
                        SELECT 1
                        FROM etapas_casos e_due
                        WHERE e_due.caso_id = c.id
                          AND e_due.estado IN ('pendiente', 'en_progreso', 'bloqueado')
                          AND e_due.fecha_estimada_fin IS NOT NULL
                          AND e_due.fecha_estimada_fin < CURDATE()
                     )";
        }

        // Ordenamiento
        $sql .= " ORDER BY c.fecha_apertura DESC, c.prioridad DESC, c.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un caso por ID
     */
    public function getById($id)
    {
        $sql = "SELECT c.*, 
                       a.nombre_completo as afiliado_nombre,
                       a.cedula as afiliado_cedula,
                       cat.nombre as categoria_nombre,
                       u_creador.nombre_completo as creado_por_nombre,
                       u_responsable.nombre_completo as responsable_nombre
                FROM {$this->table} c
                LEFT JOIN afiliados a ON c.afiliado_id = a.id
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios u_creador ON c.creado_por = u_creador.id
                LEFT JOIN usuarios u_responsable ON c.responsable_actual = u_responsable.id
                WHERE c.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo caso
     */
    public function create($datos)
    {
        $sql = "INSERT INTO {$this->table} 
                (numero_expediente, afiliado_id, categoria_id, titulo, descripcion, 
                 hechos, empresa_involucrada, departamento_afectado, estado, prioridad, 
                 fecha_incidente, fecha_apertura, creado_por, responsable_actual, observaciones)
                VALUES 
                (:numero_expediente, :afiliado_id, :categoria_id, :titulo, :descripcion,
                 :hechos, :empresa_involucrada, :departamento_afectado, :estado, :prioridad,
                 :fecha_incidente, :fecha_apertura, :creado_por, :responsable_actual, :observaciones)";

        $params = [
            'numero_expediente' => $datos['numero_expediente'] ?? null,
            'afiliado_id' => $datos['afiliado_id'] ?? null,
            'categoria_id' => $datos['categoria_id'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'hechos' => $datos['hechos'] ?? null,
            'empresa_involucrada' => $datos['empresa_involucrada'] ?? 'Banco Nacional',
            'departamento_afectado' => $datos['departamento_afectado'] ?? null,
            'estado' => $this->mapCaseStateForDb((string) ($datos['estado'] ?? 'activo')) ?? 'activo',
            'prioridad' => $datos['prioridad'] ?? 'media',
            'fecha_incidente' => $datos['fecha_incidente'] ?? null,
            'fecha_apertura' => $datos['fecha_apertura'] ?? date('Y-m-d'),
            'creado_por' => $datos['creado_por']
                ?? ($_SESSION['user_id'] ?? ($_SESSION['usuario_id'] ?? 1)),
            'responsable_actual' => $datos['responsable_actual'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($params)) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualizar caso
     */
    public function update($id, $datos)
    {
        $sql = "UPDATE {$this->table} SET 
                numero_expediente = :numero_expediente,
                afiliado_id = :afiliado_id,
                categoria_id = :categoria_id,
                titulo = :titulo,
                descripcion = :descripcion,
                hechos = :hechos,
                empresa_involucrada = :empresa_involucrada,
                departamento_afectado = :departamento_afectado,
                estado = :estado,
                prioridad = :prioridad,
                fecha_incidente = :fecha_incidente,
                responsable_actual = :responsable_actual,
                resultado_final = :resultado_final,
                observaciones = :observaciones
                WHERE id = :id";

        $params = [
            'id' => $id,
            'numero_expediente' => $datos['numero_expediente'] ?? null,
            'afiliado_id' => $datos['afiliado_id'] ?? null,
            'categoria_id' => $datos['categoria_id'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'hechos' => $datos['hechos'] ?? null,
            'empresa_involucrada' => $datos['empresa_involucrada'] ?? 'Banco Nacional',
            'departamento_afectado' => $datos['departamento_afectado'] ?? null,
            'estado' => $this->mapCaseStateForDb((string) ($datos['estado'] ?? 'activo')) ?? 'activo',
            'prioridad' => $datos['prioridad'] ?? 'media',
            'fecha_incidente' => $datos['fecha_incidente'] ?? null,
            'responsable_actual' => $datos['responsable_actual'] ?? null,
            'resultado_final' => $datos['resultado_final'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cambiar estado del caso
     */
    public function cambiarEstado($id, $nuevoEstado, ?string $resultadoFinal = null)
    {
        $nuevoEstado = $this->normalizeToken((string) $nuevoEstado);
        $estados_validos = ['activo', 'en_progreso', 'cerrado', 'archivado', 'suspendido'];

        if (!in_array($nuevoEstado, $estados_validos, true)) {
            return false;
        }

        $candidates = $this->getCaseStateCandidates($nuevoEstado);
        if (empty($candidates)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET 
                    estado = :estado_set,
                    resultado_final = CASE
                        WHEN :estado_logico = 'cerrado' AND :resultado_final_eval IS NOT NULL AND TRIM(:resultado_final_eval) <> '' 
                            THEN :resultado_final_set
                        ELSE resultado_final
                    END,
                    fecha_cierre = CASE
                        WHEN :estado_logico_cierre IN ('cerrado', 'archivado') THEN COALESCE(fecha_cierre, CURDATE())
                        ELSE NULL
                    END
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $enumValues = $this->getEnumValues('estado');

        foreach ($candidates as $candidate) {
            $estadoPersistente = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmt->execute([
                    'estado_set' => $estadoPersistente,
                    'estado_logico' => $nuevoEstado,
                    'estado_logico_cierre' => $nuevoEstado,
                    'id' => $id,
                    'resultado_final_eval' => $resultadoFinal,
                    'resultado_final_set' => $resultadoFinal
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante de estado para compatibilidad.
            }
        }

        // Fallback para esquemas antiguos sin columnas de cierre/resultado.
        $sqlSimple = "UPDATE {$this->table} SET estado = :estado_set WHERE id = :id";
        $stmtSimple = $this->db->prepare($sqlSimple);
        foreach ($candidates as $candidate) {
            $estadoPersistente = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmtSimple->execute([
                    'estado_set' => $estadoPersistente,
                    'id' => $id
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante.
            }
        }

        return false;
    }

    /**
     * Cambiar responsable del caso
     */
    public function cambiarResponsable($id, $responsableId)
    {
        $sql = "UPDATE {$this->table} 
                SET responsable_actual = :responsable_id
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['responsable_id' => ($responsableId ?: null), 'id' => $id]);
    }

    /**
     * Archivar caso
     */
    public function archivar($id, $resultado = null)
    {
        $sql = "UPDATE {$this->table} 
                SET estado = :estado_archivado, 
                    fecha_cierre = NOW(),
                    resultado_final = COALESCE(:resultado, resultado_final)
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $enumValues = $this->getEnumValues('estado');
        $candidates = $this->getCaseStateCandidates('archivado');
        if (empty($candidates)) {
            $candidates = ['archivado'];
        }

        foreach ($candidates as $candidate) {
            $estadoArchivado = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmt->execute([
                    'estado_archivado' => $estadoArchivado,
                    'resultado' => $resultado,
                    'id' => $id
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante.
            }
        }

        $sqlSimple = "UPDATE {$this->table} SET estado = :estado_archivado WHERE id = :id";
        $stmtSimple = $this->db->prepare($sqlSimple);
        foreach ($candidates as $candidate) {
            $estadoArchivado = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmtSimple->execute([
                    'estado_archivado' => $estadoArchivado,
                    'id' => $id
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante.
            }
        }

        return false;
    }

    /**
     * Eliminar caso
     */
    public function delete($id)
    {
        // Soft delete: archivar en lugar de eliminar físicamente
        $sql = "UPDATE {$this->table}
                SET estado = :estado_archivado,
                    fecha_cierre = COALESCE(fecha_cierre, CURDATE())
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $enumValues = $this->getEnumValues('estado');
        $candidates = $this->getCaseStateCandidates('archivado');
        if (empty($candidates)) {
            $candidates = ['archivado'];
        }

        foreach ($candidates as $candidate) {
            $estadoArchivado = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmt->execute([
                    'estado_archivado' => $estadoArchivado,
                    'id' => $id
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante.
            }
        }

        $sqlSimple = "UPDATE {$this->table} SET estado = :estado_archivado WHERE id = :id";
        $stmtSimple = $this->db->prepare($sqlSimple);
        foreach ($candidates as $candidate) {
            $estadoArchivado = $this->pickEnumValue($enumValues, [(string) $candidate]) ?? (string) $candidate;
            try {
                $ok = $stmtSimple->execute([
                    'estado_archivado' => $estadoArchivado,
                    'id' => $id
                ]);
                if ($ok) {
                    return true;
                }
            } catch (\Throwable $e) {
                // Intentar siguiente variante.
            }
        }

        return false;
    }

    /**
     * Obtener categorías tipo caso_rrll
     */
    public function getCategorias()
    {
        $sql = "SELECT id, nombre FROM categorias 
                WHERE estado = 'activo' AND tipo IN ('caso_rrll', 'general') 
                ORDER BY nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los afiliados activos
     */
    public function getAfiliados()
    {
        $sql = "SELECT id, nombre_completo, cedula 
                FROM afiliados 
                WHERE estado = 'activo' 
                ORDER BY nombre_completo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los usuarios responsables
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
     * Obtener etapas de un caso
     */
    public function getEtapasCaso($casoId)
    {
        $sql = "SELECT * FROM etapas_casos 
                WHERE caso_id = :caso_id 
                ORDER BY orden ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar etapas finalizadas de un caso
     */
    public function contarEtapasFinalizadas($casoId)
    {
        $sql = "SELECT COUNT(*) FROM etapas_casos 
                WHERE caso_id = :caso_id AND estado = 'finalizado'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchColumn();
    }

    /**
     * Contar total de etapas de un caso
     */
    public function contarTotalEtapas($casoId)
    {
        $sql = "SELECT COUNT(*) FROM etapas_casos WHERE caso_id = :caso_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchColumn();
    }

    /**
     * Contar etapas activas lógicas de un caso
     */
    public function contarEtapasActivas($casoId)
    {
        $sql = "SELECT COUNT(*)
                FROM etapas_casos
                WHERE caso_id = :caso_id
                  AND estado IN ('pendiente', 'en_progreso', 'bloqueado')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Obtener la etapa actual (la primera activa por orden)
     */
    public function obtenerEtapaActual($casoId)
    {
        $sql = "SELECT *
                FROM etapas_casos
                WHERE caso_id = :caso_id
                  AND estado IN ('pendiente', 'en_progreso', 'bloqueado')
                ORDER BY orden ASC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Obtener historial cronológico de caso/etapas/documentos RRLL
     */
    public function obtenerHistorialCaso($casoId, $limit = 150)
    {
        $sql = "SELECT b.*
                FROM bitacora b
                WHERE b.modulo = 'casos_rrll'
                  AND (
                        (b.entidad = 'caso' AND b.entidad_id = :caso_id)
                        OR (
                            b.entidad = 'etapa'
                            AND b.entidad_id IN (
                                SELECT e.id FROM etapas_casos e WHERE e.caso_id = :caso_id_etapa
                            )
                        )
                        OR (b.entidad = 'documento_rrll' AND b.entidad_id = :caso_id_doc)
                      )
                ORDER BY b.fecha_creacion DESC
                LIMIT :lim";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':caso_id', (int) $casoId, PDO::PARAM_INT);
        $stmt->bindValue(':caso_id_etapa', (int) $casoId, PDO::PARAM_INT);
        $stmt->bindValue(':caso_id_doc', (int) $casoId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener documentos adjuntos del caso
     */
    public function obtenerDocumentosCaso($casoId): array
    {
        $sql = "SELECT documentos_adjuntos FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $casoId]);
        $raw = (string) ($stmt->fetchColumn() ?? '');
        return $this->decodeDocumentList($raw);
    }

    /**
     * Guardar el arreglo completo de documentos de un caso
     */
    public function guardarDocumentosCaso($casoId, array $documentos): bool
    {
        $sql = "UPDATE {$this->table}
                SET documentos_adjuntos = :documentos
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'documentos' => json_encode(array_values($documentos), JSON_UNESCAPED_UNICODE),
            'id' => $casoId
        ]);
    }

    /**
     * Agregar un documento al caso
     */
    public function agregarDocumentoCaso($casoId, array $documento): bool
    {
        $documentos = $this->obtenerDocumentosCaso($casoId);
        $documentos[] = $documento;
        return $this->guardarDocumentosCaso($casoId, $documentos);
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

        // Compatibilidad con registros antiguos de texto plano (coma separada)
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

    /**
     * Genera un número de expediente único
     */
    public function generarNumeroExpediente()
    {
        $prefijo = 'EXP-' . date('Y') . '-';
        $sql = "SELECT MAX(CAST(SUBSTRING(numero_expediente, -5) AS UNSIGNED)) as max_num 
                FROM {$this->table} 
                WHERE numero_expediente LIKE :prefijo";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prefijo' => $prefijo . '%']);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $numero = ($resultado['max_num'] ?? 0) + 1;
        return $prefijo . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}
