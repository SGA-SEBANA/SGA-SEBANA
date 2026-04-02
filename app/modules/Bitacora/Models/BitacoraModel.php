<?php

namespace App\Modules\Bitacora\Models;

use App\Core\ModelBase;
use PDO;

class BitacoraModel extends ModelBase
{
    protected $table = 'bitacora';

    private const ACTION_LABELS = [
        'LOGIN' => 'Inicio de sesion',
        'LOGIN_FAILED' => 'Inicio de sesion fallido',
        'LOGOUT' => 'Cierre de sesion',
        'CREATE' => 'Creacion',
        'READ' => 'Consulta',
        'UPDATE' => 'Actualizacion',
        'DELETE' => 'Eliminacion',
        'STATUS_CHANGE' => 'Cambio de estado',
        'FINALIZAR' => 'Finalizacion',
        'ACTIVAR' => 'Activacion',
        'SUBMIT' => 'Envio',
        'APPROVE' => 'Aprobacion',
        'REJECT' => 'Rechazo',
        'CANCEL' => 'Cancelacion',
        'RESCHEDULE' => 'Reprogramacion',
        'UPLOAD_FILE' => 'Carga de archivo',
        'EXPORT' => 'Exportacion',
        'UNKNOWN' => 'Desconocida'
    ];

    private const MODULE_LABELS = [
        'auth' => 'Autenticacion',
        'usuarios' => 'Usuarios',
        'users' => 'Usuarios',
        'afiliados' => 'Afiliados',
        'asistente_afiliacion' => 'Asistente de afiliacion',
        'casos_rrll' => 'Casos de RRLL',
        'categorias' => 'Categorias',
        'oficinas' => 'Oficinas',
        'puestos' => 'Puestos',
        'junta_directiva' => 'Junta directiva',
        'vacaciones' => 'Vacaciones',
        'ayudas' => 'Ayudas economicas',
        'ayuda_economica' => 'Ayudas economicas',
        'viaticos' => 'Viaticos',
        'visitas' => 'Visitas',
        'carne' => 'Carnes',
        'reportes' => 'Reportes',
        'sistema' => 'Sistema'
    ];

    private const ENTITY_LABELS = [
        'usuario' => 'Usuario',
        'afiliado' => 'Afiliado',
        'categoria' => 'Categoria',
        'puesto' => 'Puesto',
        'oficina' => 'Oficina',
        'solicitud_ayuda' => 'Solicitud de ayuda',
        'solicitud_viatico' => 'Solicitud de viaticos',
        'solicitud_vacaciones' => 'Solicitud de vacaciones',
        'solicitud_visita' => 'Solicitud de visita',
        'solicitud_afiliacion' => 'Solicitud de afiliacion'
    ];

    private const RESULT_LABELS = [
        'exitoso' => 'Exitoso',
        'fallido' => 'Fallido',
        'bloqueado' => 'Bloqueado'
    ];

    public static function actionLabel(?string $action): string
    {
        $key = strtoupper(trim((string) $action));
        if ($key === '') {
            return '-';
        }

        return self::ACTION_LABELS[$key] ?? self::humanize($key);
    }

    public static function moduleLabel(?string $module): string
    {
        $key = strtolower(trim((string) $module));
        if ($key === '') {
            return '-';
        }

        return self::MODULE_LABELS[$key] ?? self::humanize($key);
    }

    public static function entityLabel(?string $entity): string
    {
        $key = strtolower(trim((string) $entity));
        if ($key === '') {
            return '-';
        }

        return self::ENTITY_LABELS[$key] ?? self::humanize($key);
    }

    public static function resultLabel(?string $result): string
    {
        $key = strtolower(trim((string) $result));
        if ($key === '') {
            return '-';
        }

        return self::RESULT_LABELS[$key] ?? self::humanize($key);
    }

    public function getBitacora($filtros = [], $start = 0, $limit = 10): array
    {
        $sql = "SELECT
                    id,
                    usuario_id,
                    accion,
                    modulo,
                    entidad,
                    entidad_id,
                    descripcion,
                    datos_anteriores,
                    datos_nuevos,
                    ip_address,
                    user_agent,
                    metodo_http,
                    resultado,
                    codigo_error,
                    mensaje_error,
                    url_accedida,
                    duracion_ms,
                    fecha_creacion
                FROM {$this->table}
                WHERE 1=1";
        $params = [];

        $this->appendFilters($sql, $params, $filtros);

        $sql .= ' ORDER BY fecha_creacion DESC';
        if ($limit !== null) {
            $sql .= ' LIMIT :offset, :limit';
            $params['offset'] = max((int) $start, 0);
            $params['limit'] = max((int) $limit, 1);
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map([$this, 'decorateRecord'], $rows);
    }

    public function getModulos(): array
    {
        $sql = "SELECT DISTINCT modulo FROM {$this->table} WHERE modulo IS NOT NULL AND modulo <> '' ORDER BY modulo";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['modulo_label'] = self::moduleLabel($row['modulo'] ?? '');
        }
        unset($row);

        usort($rows, static function (array $a, array $b): int {
            return strcmp((string) ($a['modulo_label'] ?? ''), (string) ($b['modulo_label'] ?? ''));
        });

        return $rows;
    }

    public function getAcciones(): array
    {
        $sql = "SELECT DISTINCT accion FROM {$this->table} WHERE accion IS NOT NULL AND accion <> '' ORDER BY accion";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['accion_label'] = self::actionLabel($row['accion'] ?? '');
        }
        unset($row);

        usort($rows, static function (array $a, array $b): int {
            return strcmp((string) ($a['accion_label'] ?? ''), (string) ($b['accion_label'] ?? ''));
        });

        return $rows;
    }

    public function getResultados(): array
    {
        $sql = "SELECT DISTINCT resultado FROM {$this->table} WHERE resultado IS NOT NULL AND resultado <> '' ORDER BY resultado";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $row['resultado_label'] = self::resultLabel($row['resultado'] ?? '');
        }
        unset($row);

        return $rows;
    }

    public function getBitacoraById($id): ?array
    {
        $sql = "SELECT
                    id,
                    usuario_id,
                    accion,
                    modulo,
                    entidad,
                    entidad_id,
                    descripcion,
                    datos_anteriores,
                    datos_nuevos,
                    ip_address,
                    user_agent,
                    metodo_http,
                    resultado,
                    codigo_error,
                    mensaje_error,
                    url_accedida,
                    duracion_ms,
                    fecha_creacion
                FROM {$this->table}
                WHERE id = :id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->decorateRecord($row) : null;
    }

    public function countBitacora($filtros = []): int
    {
        $sql = "SELECT COUNT(*) AS total FROM {$this->table} WHERE 1=1";
        $params = [];

        $this->appendFilters($sql, $params, $filtros);

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $type);
        }
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    private function appendFilters(string &$sql, array &$params, array $filtros): void
    {
        $busqueda = trim((string) ($filtros['busqueda'] ?? ''));
        if ($busqueda !== '') {
            $sql .= " AND (
                        accion LIKE :b
                        OR modulo LIKE :b
                        OR entidad LIKE :b
                        OR descripcion LIKE :b
                        OR resultado LIKE :b
                        OR IFNULL(mensaje_error, '') LIKE :b";
            $params['b'] = '%' . $busqueda . '%';

            $needle = self::normalizeTerm($busqueda);
            $matchedActions = $this->findLabelMatches(self::ACTION_LABELS, $needle);
            $matchedModules = $this->findLabelMatches(self::MODULE_LABELS, $needle);
            $matchedEntities = $this->findLabelMatches(self::ENTITY_LABELS, $needle);
            $matchedResults = $this->findLabelMatches(self::RESULT_LABELS, $needle);

            $this->appendInClause($sql, $params, 'accion', $matchedActions, 'm_accion_');
            $this->appendInClause($sql, $params, 'modulo', $matchedModules, 'm_modulo_');
            $this->appendInClause($sql, $params, 'entidad', $matchedEntities, 'm_entidad_');
            $this->appendInClause($sql, $params, 'resultado', $matchedResults, 'm_resultado_');

            $sql .= ')';
        }

        if (!empty($filtros['modulo'])) {
            $sql .= ' AND modulo = :modulo';
            $params['modulo'] = (string) $filtros['modulo'];
        }

        if (!empty($filtros['accion'])) {
            $sql .= ' AND accion = :accion';
            $params['accion'] = (string) $filtros['accion'];
        }

        if (!empty($filtros['resultado'])) {
            $sql .= ' AND resultado = :resultado';
            $params['resultado'] = (string) $filtros['resultado'];
        }

        if (!empty($filtros['fecha'])) {
            $sql .= ' AND DATE(fecha_creacion) = :fecha';
            $params['fecha'] = (string) $filtros['fecha'];
        }
    }

    private function findLabelMatches(array $labels, string $needle): array
    {
        if ($needle === '') {
            return [];
        }

        $matches = [];
        foreach ($labels as $key => $label) {
            $keyNorm = self::normalizeTerm((string) $key);
            $labelNorm = self::normalizeTerm((string) $label);
            if (
                str_contains($keyNorm, $needle) ||
                str_contains($labelNorm, $needle) ||
                str_contains($needle, $keyNorm) ||
                str_contains($needle, $labelNorm)
            ) {
                $matches[] = (string) $key;
            }
        }

        return array_values(array_unique($matches));
    }

    private function appendInClause(string &$sql, array &$params, string $column, array $values, string $prefix): void
    {
        if (empty($values)) {
            return;
        }

        $placeholders = [];
        foreach (array_values($values) as $index => $value) {
            $key = $prefix . $index;
            $placeholders[] = ':' . $key;
            $params[$key] = $value;
        }

        $sql .= ' OR ' . $column . ' IN (' . implode(', ', $placeholders) . ')';
    }

    private static function normalizeTerm(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        if (function_exists('iconv')) {
            $converted = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = strtolower($converted);
            }
        }

        return preg_replace('/\s+/', ' ', $value) ?? '';
    }

    private static function humanize(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '-';
        }

        $value = strtolower(str_replace(['_', '-'], ' ', $value));
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return ucwords($value);
    }

    private function decorateRecord(array $record): array
    {
        $record['accion_label'] = self::actionLabel($record['accion'] ?? '');
        $record['modulo_label'] = self::moduleLabel($record['modulo'] ?? '');
        $record['entidad_label'] = self::entityLabel($record['entidad'] ?? '');
        $record['resultado_label'] = self::resultLabel($record['resultado'] ?? '');
        return $record;
    }
}
