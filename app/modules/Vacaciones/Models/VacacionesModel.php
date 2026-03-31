<?php
namespace App\Modules\Vacaciones\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class VacacionesModel extends ModelBase
{
    protected $table = 'solicitudes_vacaciones';
    private $lastError = '';
    private $storageMode = true;
    private $storageDir;

    public function __construct()
    {
        parent::__construct();
        $config = require BASE_PATH . '/app/config/config.php';
        $this->storageMode = $config['storage_mode'] ?? true;
        $this->storageDir = BASE_PATH . '/storage/vacaciones/requests';
    }

    private function ensureStorageDir()
    {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
    }

    private function storagePath($id)
    {
        return $this->storageDir . '/' . $id . '.json';
    }

    private function generateStorageId()
    {
        $this->ensureStorageDir();
        $id = (int) round(microtime(true) * 1000);
        $path = $this->storagePath($id);

        while (file_exists($path)) {
            $id++;
            $path = $this->storagePath($id);
        }

        return $id;
    }

    private function readStorage($id)
    {
        $path = $this->storagePath($id);
        if (!file_exists($path)) {
            return null;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return null;
        }

        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }

    private function writeStorage(array $record)
    {
        $this->ensureStorageDir();

        if (empty($record['id'])) {
            return false;
        }

        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->lastError = 'No se pudo serializar la solicitud.';
            return false;
        }

        $ok = file_put_contents($this->storagePath($record['id']), $json) !== false;
        if (!$ok) {
            $this->lastError = 'No se pudo guardar la solicitud.';
        }

        return $ok;
    }

    private function listStorage()
    {
        $this->ensureStorageDir();
        $rows = [];
        $files = glob($this->storageDir . '/*.json');

        if (!$files) {
            return $rows;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $data = json_decode($content, true);
            if (is_array($data)) {
                $rows[] = $data;
            }
        }

        return $rows;
    }

    private function normalizeState($estado)
    {
        $estado = strtolower(trim((string) $estado));
        $estado = str_replace(['_', '-'], ' ', $estado);
        return preg_replace('/\s+/', ' ', $estado) ?: '';
    }

    private function mapEstadoToDb($estado)
    {
        $normalizado = $this->normalizeState($estado);

        switch ($normalizado) {
            case 'pendiente':
                return 'pendiente';
            case 'aceptada':
            case 'aprobada':
                return 'aprobada';
            case 'rechazada':
                return 'rechazada';
            case 'cancelada':
                return 'cancelada';
            case 'reprogramada':
                return 'reprogramada';
            case 'en revision':
                return 'en_revision';
            default:
                return 'pendiente';
        }
    }

    private function mapEstadoFromDb($estadoDb)
    {
        switch ((string) $estadoDb) {
            case 'pendiente':
                return 'Pendiente';
            case 'aprobada':
                return 'Aceptada';
            case 'rechazada':
                return 'Rechazada';
            case 'cancelada':
                return 'Cancelada';
            case 'reprogramada':
                return 'Reprogramada';
            case 'en_revision':
                return 'En Revision';
            default:
                return ucfirst((string) $estadoDb);
        }
    }

    private function calcularCantidadDias($fechaInicio, $fechaFin)
    {
        try {
            $inicio = new \DateTime($fechaInicio);
            $fin = new \DateTime($fechaFin);
            if ($fin < $inicio) {
                return null;
            }

            return (int) $inicio->diff($fin)->days + 1;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function obtenerAfiliadoIdPorUsuario($usuarioId)
    {
        if (empty($usuarioId)) {
            return null;
        }

        try {
            $stmt = $this->db->prepare('SELECT id FROM afiliados WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $usuarioId]);
            $direct = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($direct && !empty($direct['id'])) {
                return (int) $direct['id'];
            }

            $stmt = $this->db->prepare(
                "SELECT a.id
                 FROM usuarios u
                 INNER JOIN afiliados a ON a.correo = u.correo
                 WHERE u.id = :usuario_id
                 LIMIT 1"
            );
            $stmt->execute([':usuario_id' => $usuarioId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['id'])) {
                return (int) $row['id'];
            }

            $stmt = $this->db->prepare(
                "SELECT a.id
                 FROM usuarios u
                 INNER JOIN afiliados a ON (a.cedula = u.username OR a.correo = u.username)
                 WHERE u.id = :usuario_id
                 LIMIT 1"
            );
            $stmt->execute([':usuario_id' => $usuarioId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['id'])) {
                return (int) $row['id'];
            }
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
        }

        return null;
    }

    private function obtenerUsuario($usuarioId)
    {
        try {
            $stmt = $this->db->prepare('SELECT id, correo, nombre_completo FROM usuarios WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $usuarioId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function obtenerNombreCorreoAfiliado($afiliadoId)
    {
        if (empty($afiliadoId)) {
            return [null, null];
        }

        try {
            $stmt = $this->db->prepare('SELECT nombre_completo, correo FROM afiliados WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $afiliadoId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return [$row['nombre_completo'] ?? null, $row['correo'] ?? null];
            }
        } catch (PDOException $e) {
        }

        return [null, null];
    }

    private function mapRow(array $row)
    {
        return [
            'id' => (int) ($row['id'] ?? 0),
            'codigo_solicitud' => $row['codigo_solicitud'] ?? null,
            'usuario_id' => isset($row['usuario_id']) ? (int) $row['usuario_id'] : null,
            'afiliado_id' => isset($row['afiliado_id']) ? (int) $row['afiliado_id'] : null,
            'nombre_completo' => $row['nombre_completo'] ?? 'Afiliado',
            'correo' => $row['correo'] ?? null,
            'fecha_inicio' => $row['fecha_inicio'] ?? null,
            'fecha_fin' => $row['fecha_fin'] ?? null,
            'cantidad_dias' => (int) ($row['cantidad_dias'] ?? 0),
            'motivo' => $row['motivo'] ?? '',
            'tipo_vacaciones' => $row['tipo_vacaciones'] ?? 'ordinarias',
            'estado' => $row['estado_ui'] ?? $this->mapEstadoFromDb($row['estado'] ?? ''),
            'fecha_reprogramacion' => $row['fecha_reprogramacion'] ?? null,
            'motivo_reprogramacion' => $row['motivo_reprogramacion'] ?? null,
            'motivo_cancelacion' => $row['motivo_cancelacion'] ?? null,
            'motivo_rechazo' => $row['motivo_rechazo'] ?? null,
            'fecha_creacion' => $row['fecha_creacion'] ?? null,
            'fecha_actualizacion' => $row['fecha_actualizacion'] ?? null
        ];
    }

    public function crearSolicitud($usuarioId, $fechaInicio, $fechaFin, $motivo)
    {
        $this->lastError = '';

        $cantidadDias = $this->calcularCantidadDias($fechaInicio, $fechaFin);
        if ($cantidadDias === null || $cantidadDias <= 0) {
            $this->lastError = 'Rango de fechas invalido.';
            return false;
        }

        if ($this->storageMode) {
            return $this->crearSolicitudStorage($usuarioId, $fechaInicio, $fechaFin, $motivo, $cantidadDias);
        }

        $afiliadoId = $this->obtenerAfiliadoIdPorUsuario($usuarioId);
        if (!$afiliadoId) {
            $this->lastError = 'No se pudo asociar el usuario a un afiliado.';
            return false;
        }

        try {
            $sql = "INSERT INTO {$this->table}
                        (afiliado_id, fecha_inicio, fecha_fin, cantidad_dias, motivo, tipo_vacaciones, estado)
                    VALUES
                        (:afiliado_id, :fecha_inicio, :fecha_fin, :cantidad_dias, :motivo, 'ordinarias', 'pendiente')";
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute([
                ':afiliado_id' => $afiliadoId,
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin' => $fechaFin,
                ':cantidad_dias' => $cantidadDias,
                ':motivo' => trim((string) $motivo)
            ]);

            return $ok ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    private function crearSolicitudStorage($usuarioId, $fechaInicio, $fechaFin, $motivo, $cantidadDias)
    {
        $usuario = $this->obtenerUsuario($usuarioId);
        $afiliadoId = $this->obtenerAfiliadoIdPorUsuario($usuarioId);
        [$nombreAfiliado, $correoAfiliado] = $this->obtenerNombreCorreoAfiliado($afiliadoId);
        $now = date('Y-m-d H:i:s');

        $record = [
            'id' => $this->generateStorageId(),
            'codigo_solicitud' => null,
            'usuario_id' => $usuarioId,
            'afiliado_id' => $afiliadoId,
            'nombre_completo' => $usuario['nombre_completo'] ?? $nombreAfiliado ?? ('Usuario #' . $usuarioId),
            'correo' => $usuario['correo'] ?? $correoAfiliado,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'cantidad_dias' => $cantidadDias,
            'motivo' => trim((string) $motivo),
            'tipo_vacaciones' => 'ordinarias',
            'estado' => 'Pendiente',
            'fecha_reprogramacion' => null,
            'motivo_reprogramacion' => null,
            'motivo_cancelacion' => null,
            'motivo_rechazo' => null,
            'fecha_creacion' => $now,
            'fecha_actualizacion' => $now
        ];

        if (!$this->writeStorage($record)) {
            return false;
        }

        return $record['id'];
    }

    public function obtenerPorUsuario($usuarioId)
    {
        $afiliadoId = $this->obtenerAfiliadoIdPorUsuario($usuarioId);
        $rows = [];
        $storageIds = [];

        foreach ($this->listStorage() as $record) {
            $isMine = ((int) ($record['usuario_id'] ?? 0) === (int) $usuarioId);
            if (!$isMine && $afiliadoId) {
                $isMine = ((int) ($record['afiliado_id'] ?? 0) === (int) $afiliadoId);
            }

            if ($isMine) {
                $rows[] = $record;
                $storageIds[(string) $record['id']] = true;
            }
        }

        if ($afiliadoId) {
            $sql = "SELECT v.*,
                           u.id AS usuario_id,
                           COALESCE(u.nombre_completo, a.nombre_completo) AS nombre_completo,
                           COALESCE(u.correo, a.correo) AS correo
                    FROM {$this->table} v
                    INNER JOIN afiliados a ON v.afiliado_id = a.id
                    LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                    WHERE v.afiliado_id = :afiliado_id
                    ORDER BY v.fecha_creacion DESC";
            try {
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':afiliado_id' => $afiliadoId]);
                $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($dbRows as $row) {
                    if (!isset($storageIds[(string) $row['id']])) {
                        $rows[] = $this->mapRow($row);
                    }
                }
            } catch (PDOException $e) {
            }
        }

        usort($rows, function ($a, $b) {
            $fa = strtotime($a['fecha_creacion'] ?? '1970-01-01');
            $fb = strtotime($b['fecha_creacion'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $rows;
    }

    public function obtenerTodasConUsuario()
    {
        $rows = [];
        $storageIds = [];

        foreach ($this->listStorage() as $record) {
            $rows[] = $record;
            $storageIds[(string) $record['id']] = true;
        }

        $sql = "SELECT v.*,
                       u.id AS usuario_id,
                       COALESCE(u.nombre_completo, a.nombre_completo) AS nombre_completo,
                       COALESCE(u.correo, a.correo) AS correo
                FROM {$this->table} v
                INNER JOIN afiliados a ON v.afiliado_id = a.id
                LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                ORDER BY v.fecha_creacion DESC";

        try {
            $stmt = $this->db->query($sql);
            $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dbRows as $row) {
                if (!isset($storageIds[(string) $row['id']])) {
                    $rows[] = $this->mapRow($row);
                }
            }
        } catch (PDOException $e) {
        }

        usort($rows, function ($a, $b) {
            $fa = strtotime($a['fecha_creacion'] ?? '1970-01-01');
            $fb = strtotime($b['fecha_creacion'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $rows;
    }

    public function obtenerDetallePorId($id)
    {
        $storage = $this->readStorage($id);
        if ($storage) {
            return $storage;
        }

        $sql = "SELECT v.*,
                       u.id AS usuario_id,
                       COALESCE(u.nombre_completo, a.nombre_completo) AS nombre_completo,
                       COALESCE(u.correo, a.correo) AS correo
                FROM {$this->table} v
                INNER JOIN afiliados a ON v.afiliado_id = a.id
                LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                WHERE v.id = :id
                LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->mapRow($row) : false;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function cambiarEstado($id, $nuevoEstado, $actorUserId = null)
    {
        $this->lastError = '';
        $estadoDb = $this->mapEstadoToDb($nuevoEstado);
        $estadoUi = $this->mapEstadoFromDb($estadoDb);

        $record = $this->readStorage($id);
        if ($record) {
            $record['estado'] = $estadoUi;
            $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
            if ($estadoDb === 'cancelada' && empty($record['motivo_cancelacion'])) {
                $record['motivo_cancelacion'] = 'Cancelada por usuario';
            }
            return $this->writeStorage($record);
        }

        try {
            $sql = "UPDATE {$this->table}
                    SET estado = :estado,
                        revisado_por = :revisado_por,
                        fecha_revision = NOW(),
                        fecha_aprobacion = CASE WHEN :estado_aprobado = 1 THEN NOW() ELSE fecha_aprobacion END,
                        aprobado_por = CASE WHEN :estado_aprobado = 1 THEN :aprobado_por ELSE aprobado_por END
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':estado' => $estadoDb,
                ':revisado_por' => $actorUserId,
                ':estado_aprobado' => ($estadoDb === 'aprobada') ? 1 : 0,
                ':aprobado_por' => ($estadoDb === 'aprobada') ? $actorUserId : null,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function reprogramarSolicitud($id, $fechaInicio, $fechaFin, $motivo)
    {
        $this->lastError = '';
        $cantidadDias = $this->calcularCantidadDias($fechaInicio, $fechaFin);
        if ($cantidadDias === null || $cantidadDias <= 0) {
            $this->lastError = 'Rango de fechas invalido.';
            return false;
        }

        $record = $this->readStorage($id);
        if ($record) {
            $record['fecha_inicio'] = $fechaInicio;
            $record['fecha_fin'] = $fechaFin;
            $record['cantidad_dias'] = $cantidadDias;
            $record['motivo'] = trim((string) $motivo);
            $record['estado'] = 'Pendiente';
            $record['fecha_reprogramacion'] = date('Y-m-d');
            $record['motivo_reprogramacion'] = trim((string) $motivo);
            $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
            return $this->writeStorage($record);
        }

        try {
            $sql = "UPDATE {$this->table}
                    SET fecha_inicio = :fecha_inicio,
                        fecha_fin = :fecha_fin,
                        cantidad_dias = :cantidad_dias,
                        motivo = :motivo,
                        estado = 'pendiente',
                        fecha_reprogramacion = CURDATE(),
                        motivo_reprogramacion = :motivo_reprogramacion
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':fecha_inicio' => $fechaInicio,
                ':fecha_fin' => $fechaFin,
                ':cantidad_dias' => $cantidadDias,
                ':motivo' => trim((string) $motivo),
                ':motivo_reprogramacion' => trim((string) $motivo),
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getLastError()
    {
        return $this->lastError;
    }
}
