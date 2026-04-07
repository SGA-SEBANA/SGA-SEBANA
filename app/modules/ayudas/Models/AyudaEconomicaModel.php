<?php
namespace App\Modules\Ayudas\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class AyudaEconomicaModel extends ModelBase {
    
    protected $table = 'solicitudes_ayuda_economica';
    protected $table_evidencias = 'archivos_adjuntos';
    protected $lastError = null;
    private $storageMode = true;
    private $storageDir;
    private $storageFilesDir;

    private const VIATICO_MARK = '"__tipo":"viatico"';

    public function __construct() {
        parent::__construct();
        $config = require BASE_PATH . '/app/config/config.php';
        $this->storageMode = $config['storage_mode'] ?? true;
        $this->storageDir = BASE_PATH . '/storage/ayudas/requests';
        $this->storageFilesDir = BASE_PATH . '/storage/ayudas';
    }

    public function getLastError() {
        return $this->lastError;
    }

    private function ensureStorageDirs() {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }
        if (!is_dir($this->storageFilesDir)) {
            mkdir($this->storageFilesDir, 0755, true);
        }
    }

    private function storagePath($id) {
        return $this->storageDir . '/' . $id . '.json';
    }

    private function generateStorageId() {
        $this->ensureStorageDirs();
        $id = (int)round(microtime(true) * 1000);
        $path = $this->storagePath($id);
        while (file_exists($path)) {
            $id++;
            $path = $this->storagePath($id);
        }
        return $id;
    }

    private function readStorage($id) {
        $path = $this->storagePath($id);
        if (!file_exists($path)) {
            return null;
        }
        $contenido = file_get_contents($path);
        if ($contenido === false) {
            return null;
        }
        $data = json_decode($contenido, true);
        return is_array($data) ? $data : null;
    }

    private function writeStorage(array $record) {
        $this->ensureStorageDirs();
        if (empty($record['id'])) {
            return false;
        }
        $path = $this->storagePath($record['id']);
        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->lastError = 'No se pudo serializar el registro.';
            return false;
        }
        $ok = file_put_contents($path, $json) !== false;
        if (!$ok) {
            $this->lastError = 'No se pudo escribir el archivo de solicitud.';
        }
        return $ok;
    }

    private function listStorage() {
        $this->ensureStorageDirs();
        $records = [];
        $files = glob($this->storageDir . '/*.json');
        if (!$files) {
            return $records;
        }
        foreach ($files as $file) {
            $contenido = file_get_contents($file);
            if ($contenido === false) {
                continue;
            }
            $data = json_decode($contenido, true);
            if (is_array($data)) {
                $records[] = $data;
            }
        }
        return $records;
    }

    private function mapEstadoToDb($estado) {
        $normalizado = strtolower(trim($estado));
        switch ($normalizado) {
            case 'pendiente':
                return 'pendiente';
            case 'aprobada':
                return 'aprobada';
            case 'rechazada':
                return 'rechazada';
            case 'cancelada':
                return 'cancelada';
            case 'cancelación solicitada':
            case 'cancelacion solicitada':
                return 'en_revision';
            case 'reevaluada':
                return 'reevaluada';
            case 'desembolsada':
                return 'desembolsada';
            case 'en revisión':
            case 'en revision':
                return 'en_revision';
            default:
                return 'pendiente';
        }
    }

    private function mapEstadoFromDb(array $row) {
        $estadoDb = $row['estado'] ?? '';
        switch ($estadoDb) {
            case 'pendiente':
                return 'Pendiente';
            case 'aprobada':
                return 'Aprobada';
            case 'rechazada':
                return 'Rechazada';
            case 'cancelada':
                return 'Cancelada';
            case 'en_revision':
                return !empty($row['motivo_cancelacion']) ? 'Cancelación Solicitada' : 'En Revisión';
            case 'reevaluada':
                return 'Reevaluada';
            case 'desembolsada':
                return 'Desembolsada';
            default:
                return ucfirst($estadoDb);
        }
    }

    private function resolverAfiliadoId($usuario_id) {
        if (empty($usuario_id)) {
            return null;
        }

        try {
            // 1) Si el ID del usuario coincide con un afiliado (caso común en algunos entornos)
            $stmt = $this->db->prepare("SELECT id FROM afiliados WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuario_id]);
            $directo = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($directo && !empty($directo['id'])) {
                return (int)$directo['id'];
            }

            // 2) Intentar resolver por correo (usuarios -> afiliados)
            $stmt = $this->db->prepare("
                SELECT a.id
                FROM usuarios u
                INNER JOIN afiliados a ON a.correo = u.correo
                WHERE u.id = :usuario_id
                LIMIT 1
            ");
            $stmt->execute([':usuario_id' => $usuario_id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fila && !empty($fila['id'])) {
                return (int)$fila['id'];
            }

            // 3) Intentar resolver por username = cédula o correo
            $stmt = $this->db->prepare("
                SELECT a.id
                FROM usuarios u
                INNER JOIN afiliados a ON (a.cedula = u.username OR a.correo = u.username)
                WHERE u.id = :usuario_id
                LIMIT 1
            ");
            $stmt->execute([':usuario_id' => $usuario_id]);
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fila && !empty($fila['id'])) {
                return (int)$fila['id'];
            }

            // 4) Fallback final: primer afiliado activo
            $stmt = $this->db->query("SELECT id FROM afiliados WHERE estado = 'activo' ORDER BY id ASC LIMIT 1");
            $fallback = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fallback && !empty($fallback['id'])) {
                error_log("Usando afiliado_id de respaldo para usuario_id {$usuario_id}: " . $fallback['id']);
                return (int)$fallback['id'];
            }

            return null;
        } catch (PDOException $e) {
            error_log("Error resolviendo afiliado_id: " . $e->getMessage());
            return null;
        }
    }

    public function obtenerAfiliadoIdPorUsuario($usuario_id) {
        return $this->resolverAfiliadoId($usuario_id);
    }

    public function resolveUsuarioIdPorAfiliado($afiliado_id) {
        $afiliado_id = (int) $afiliado_id;
        if ($afiliado_id <= 0) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT u.id
                FROM afiliados a
                INNER JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                WHERE a.id = :afiliado_id
                AND u.estado = 'activo'
                ORDER BY u.id ASC
                LIMIT 1
            ");
            $stmt->execute([':afiliado_id' => $afiliado_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row && !empty($row['id'])) {
                return (int) $row['id'];
            }
        } catch (PDOException $e) {
            error_log("Error resolviendo usuario por afiliado en ayudas: " . $e->getMessage());
        }

        return null;
    }

    private function obtenerUsuario($usuario_id) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, correo, nombre_completo FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuario_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function mapDbRowToStorage(array $row) {
        return [
            'id' => (int)$row['id'],
            'usuario_id' => $row['usuario_id'] ?? null,
            'afiliado_id' => $row['afiliado_id'] ?? null,
            'nombre_completo' => $row['nombre_completo'] ?? 'Sin nombre',
            'correo' => $row['correo'] ?? null,
            'motivo' => $row['motivo'] ?? '',
            'descripcion_detallada' => $row['descripcion_detallada'] ?? ($row['motivo'] ?? ''),
            'monto_solicitado' => (float)($row['monto_solicitado'] ?? 0),
            'estado' => $this->mapEstadoFromDb($row),
            'motivo_cancelacion' => $row['motivo_cancelacion'] ?? null,
            'fecha_solicitud' => $row['fecha_creacion'] ?? null,
            'fecha_creacion' => $row['fecha_creacion'] ?? null,
            'fecha_actualizacion' => $row['fecha_actualizacion'] ?? null,
            'evidencias' => []
        ];
    }

    private function fetchDbRow($id) {
        $sql = "SELECT s.*, a.nombre_completo, a.correo, u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON u.correo = a.correo
                WHERE s.id = :id
                AND s.descripcion_detallada NOT LIKE :marker
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':marker' => '%' . self::VIATICO_MARK . '%'
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    private function ensureStorageRecord($id) {
        $record = $this->readStorage($id);
        if ($record) {
            return $record;
        }
        $row = $this->fetchDbRow($id);
        if (!$row) {
            return null;
        }
        $record = $this->mapDbRowToStorage($row);
        $this->writeStorage($record);
        return $record;
    }

    public function crearSolicitud($usuario_id, $motivo, $monto_solicitado, $afiliado_id_override = null) {
        $this->lastError = null;
        if ($this->storageMode) {
            return $this->crearSolicitudStorage($usuario_id, $motivo, $monto_solicitado, $afiliado_id_override);
        }

        $afiliado_id = !empty($afiliado_id_override)
            ? (int) $afiliado_id_override
            : $this->resolverAfiliadoId($usuario_id);
        if (!$afiliado_id) {
            $this->lastError = "No se pudo resolver afiliado_id para usuario_id: " . $usuario_id;
            return false;
        }

        $motivo = trim((string)$motivo);
        if ($motivo === '') {
            $motivo = 'Sin detalle';
        }
        $motivoCorto = $motivo;
        if (strlen($motivoCorto) > 250) {
            $motivoCorto = substr($motivoCorto, 0, 250);
        }

        $sql = "INSERT INTO {$this->table} 
                    (afiliado_id, motivo, descripcion_detallada, monto_solicitado, tipo_ayuda, estado) 
                VALUES 
                    (:afiliado_id, :motivo, :descripcion_detallada, :monto_solicitado, 'otra', 'pendiente')";
        
        try {
            $stmt = $this->db->prepare($sql);
            $exito = $stmt->execute([
                ':afiliado_id' => $afiliado_id,
                ':motivo' => $motivoCorto,
                ':descripcion_detallada' => $motivo,
                ':monto_solicitado' => $monto_solicitado
            ]);
            
            return $exito ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("Error creando solicitud de ayuda: " . $e->getMessage());
            return false;
        }
    }

    private function crearSolicitudStorage($usuario_id, $motivo, $monto_solicitado, $afiliado_id_override = null) {
        $afiliado_id = !empty($afiliado_id_override)
            ? (int) $afiliado_id_override
            : $this->resolverAfiliadoId($usuario_id);
        $usuario = $this->obtenerUsuario($usuario_id);

        $motivo = trim((string)$motivo);
        if ($motivo === '') {
            $motivo = 'Sin detalle';
        }
        $motivoCorto = $motivo;
        if (strlen($motivoCorto) > 250) {
            $motivoCorto = substr($motivoCorto, 0, 250);
        }

        $now = date('Y-m-d H:i:s');
        $id = $this->generateStorageId();
        $record = [
            'id' => $id,
            'usuario_id' => $usuario_id,
            'afiliado_id' => $afiliado_id,
            'nombre_completo' => $usuario['nombre_completo'] ?? ('Usuario #' . $usuario_id),
            'correo' => $usuario['correo'] ?? null,
            'motivo' => $motivoCorto,
            'descripcion_detallada' => $motivo,
            'monto_solicitado' => (float)$monto_solicitado,
            'estado' => 'Pendiente',
            'motivo_cancelacion' => null,
            'fecha_solicitud' => $now,
            'fecha_creacion' => $now,
            'fecha_actualizacion' => $now,
            'evidencias' => []
        ];

        if (!$this->writeStorage($record)) {
            return false;
        }

        return $id;
    }

    /**
     * HU-SAEC-1 Escenario 2: Registro de solicitud de cancelación por el usuario.
     */
    public function registrarCancelacionUsuario($id, $motivo_cancelacion) {
        $record = $this->ensureStorageRecord($id);
        if (!$record) {
            $this->lastError = 'Solicitud no encontrada.';
            return false;
        }
        $record['estado'] = 'Cancelación Solicitada';
        $record['motivo_cancelacion'] = $motivo_cancelacion;
        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
        return $this->writeStorage($record);
    }

    public function guardarEvidencia($ayuda_id, $usuario_id, $nombre_archivo, $ruta_archivo, $estado_solicitud) {
        $record = $this->ensureStorageRecord($ayuda_id);
        if (!$record) {
            $this->lastError = 'Solicitud no encontrada para adjuntar evidencia.';
            return false;
        }

        if (!isset($record['evidencias']) || !is_array($record['evidencias'])) {
            $record['evidencias'] = [];
        }

        $usuario = $this->obtenerUsuario($usuario_id);
        $evidenciaId = (int)round(microtime(true) * 1000);
        $record['evidencias'][] = [
            'id' => $evidenciaId,
            'ayuda_id' => (int) $ayuda_id,
            'nombre_archivo' => $nombre_archivo,
            'ruta_archivo' => $ruta_archivo,
            'estado_solicitud_al_subir' => $estado_solicitud,
            'fecha_carga' => date('Y-m-d H:i:s'),
            'usuario_id' => $usuario_id,
            'nombre_completo' => $usuario['nombre_completo'] ?? ('Usuario #' . $usuario_id)
        ];

        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
        return $this->writeStorage($record);
    }

    public function obtenerPorId($id) {
        $record = $this->readStorage($id);
        if ($record) {
            return $record;
        }

        $row = $this->fetchDbRow($id);
        if (!$row) {
            return false;
        }
        $row['fecha_solicitud'] = $row['fecha_creacion'] ?? null;
        $row['estado'] = $this->mapEstadoFromDb($row);
        return $row;
    }

    public function obtenerEvidencias($ayuda_id) {
        $record = $this->readStorage($ayuda_id);
        if ($record && !empty($record['evidencias']) && is_array($record['evidencias'])) {
            return $record['evidencias'];
        }

        $sql = "SELECT 
                    e.id,
                    e.nombre_original AS nombre_archivo,
                    e.path AS ruta_archivo,
                    e.descripcion AS estado_solicitud_al_subir,
                    e.fecha_creacion AS fecha_carga,
                    u.nombre_completo
                FROM {$this->table_evidencias} e
                LEFT JOIN usuarios u ON e.subido_por = u.id
                WHERE e.entidad_tipo = 'solicitud_ayuda'
                AND e.entidad_id = :ayuda_id 
                ORDER BY e.fecha_creacion ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':ayuda_id' => $ayuda_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo evidencias: " . $e->getMessage());
            return [];
        }
    }

    public function obtenerTodas() {
        $storage = $this->listStorage();
        $storageById = [];
        foreach ($storage as $record) {
            if (!empty($record['id'])) {
                $storageById[$record['id']] = $record;
            }
        }

        $rows = array_values($storageById);

        $sql = "SELECT s.*, a.nombre_completo, a.correo, u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON u.correo = a.correo
                WHERE s.descripcion_detallada NOT LIKE :marker
                ORDER BY s.fecha_creacion DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':marker' => '%' . self::VIATICO_MARK . '%']);
            $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dbRows as $row) {
                if (isset($storageById[$row['id']])) {
                    continue;
                }
                $row['fecha_solicitud'] = $row['fecha_creacion'] ?? null;
                $row['estado'] = $this->mapEstadoFromDb($row);
                $rows[] = $row;
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo todas las ayudas: " . $e->getMessage());
        }

        usort($rows, function ($a, $b) {
            $fa = strtotime($a['fecha_solicitud'] ?? $a['fecha_creacion'] ?? '1970-01-01');
            $fb = strtotime($b['fecha_solicitud'] ?? $b['fecha_creacion'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $rows;
    }

    public function obtenerPorUsuario($usuario_id) {
        $storage = $this->listStorage();
        $storageById = [];
        foreach ($storage as $record) {
            if (!empty($record['id']) && (int) ($record['usuario_id'] ?? 0) === (int) $usuario_id) {
                $storageById[$record['id']] = $record;
            }
        }

        $rows = array_values($storageById);
        $afiliado_id = $this->resolverAfiliadoId($usuario_id);

        $sql = "SELECT s.*, a.nombre_completo, a.correo, u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON u.correo = a.correo
                WHERE s.descripcion_detallada NOT LIKE :marker
                  AND (u.id = :usuario_id OR s.afiliado_id = :afiliado_id)
                ORDER BY s.fecha_creacion DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':marker' => '%' . self::VIATICO_MARK . '%',
                ':usuario_id' => $usuario_id,
                ':afiliado_id' => $afiliado_id ?: 0
            ]);
            $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($dbRows as $row) {
                if (isset($storageById[$row['id']])) {
                    continue;
                }
                $row['fecha_solicitud'] = $row['fecha_creacion'] ?? null;
                $row['estado'] = $this->mapEstadoFromDb($row);
                $rows[] = $row;
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo ayudas por usuario: " . $e->getMessage());
        }

        usort($rows, function ($a, $b) {
            $fa = strtotime($a['fecha_solicitud'] ?? $a['fecha_creacion'] ?? '1970-01-01');
            $fb = strtotime($b['fecha_solicitud'] ?? $b['fecha_creacion'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $rows;
    }

    public function obtenerEvidenciaPorId($evidencia_id) {
        $storage = $this->listStorage();
        foreach ($storage as $record) {
            if (empty($record['evidencias']) || !is_array($record['evidencias'])) {
                continue;
            }
            foreach ($record['evidencias'] as $evidencia) {
                if (isset($evidencia['id']) && (string)$evidencia['id'] === (string)$evidencia_id) {
                    if (!isset($evidencia['ayuda_id'])) {
                        $evidencia['ayuda_id'] = (int) ($record['id'] ?? 0);
                    }
                    if (!isset($evidencia['path']) && isset($evidencia['ruta_archivo'])) {
                        $evidencia['path'] = $evidencia['ruta_archivo'];
                    }
                    return $evidencia;
                }
            }
        }

        $sql = "SELECT e.*, e.entidad_id AS ayuda_id
                FROM {$this->table_evidencias} e
                WHERE e.id = :id
                AND e.entidad_tipo = 'solicitud_ayuda'
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $evidencia_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo evidencia por ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * HU-SAEC-5: Obtener correos de administradores para notificaciones de nueva evidencia.
     */
    public function obtenerCorreosAdministradores() {
        $sql = "SELECT u.correo
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE r.nivel_acceso IN ('total','alto')";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo correos de administradores: " . $e->getMessage());
            return [];
        }
    }

    public function cambiarEstado($id, $nuevo_estado) {
        $record = $this->ensureStorageRecord($id);
        if (!$record) {
            $this->lastError = 'Solicitud no encontrada.';
            return false;
        }
        $record['estado'] = $nuevo_estado;
        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
        return $this->writeStorage($record);
    }
}
