<?php
namespace App\Modules\Viaticos\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class ViaticoModel extends ModelBase {
    
    protected $table = 'solicitudes_ayuda_economica';
    protected $lastError = null;
    private $storageMode = true;
    private $storageDir;
    private $storageFilesDir;

    private const VIATICO_MARK = '"__tipo":"viatico"';

    public function __construct() {
        parent::__construct();
        $config = require BASE_PATH . '/app/config/config.php';
        $this->storageMode = $config['storage_mode'] ?? true;
        $this->storageDir = BASE_PATH . '/storage/viaticos/requests';
        $this->storageFilesDir = BASE_PATH . '/storage/viaticos';
    }

    public function getLastError() {
        return $this->lastError;
    }

    private function ensureStorageDirs() {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0777, true);
        }
        if (!is_dir($this->storageFilesDir)) {
            mkdir($this->storageFilesDir, 0777, true);
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

    private function resolverAfiliadoId($usuario_id) {
        if (empty($usuario_id)) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT id FROM afiliados WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuario_id]);
            $directo = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($directo && !empty($directo['id'])) {
                return (int)$directo['id'];
            }

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

            $creado = $this->crearAfiliadoBasicoDesdeUsuario($usuario_id);
            if ($creado) {
                return $creado;
            }

            $stmt = $this->db->query("SELECT id FROM afiliados WHERE estado = 'activo' ORDER BY id ASC LIMIT 1");
            $fallback = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($fallback && !empty($fallback['id'])) {
                error_log("Usando afiliado_id de respaldo para usuario_id {$usuario_id}: " . $fallback['id']);
                return (int)$fallback['id'];
            }

            return null;
        } catch (PDOException $e) {
            error_log("Error resolviendo afiliado_id en viáticos: " . $e->getMessage());
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
            error_log("Error resolviendo usuario por afiliado en viaticos: " . $e->getMessage());
        }

        return null;
    }

    private function crearAfiliadoBasicoDesdeUsuario($usuario_id) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, correo, nombre_completo FROM usuarios WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $usuario_id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                return null;
            }

            $nombreCompleto = trim($usuario['nombre_completo'] ?? '');
            $partes = preg_split('/\s+/', $nombreCompleto, -1, PREG_SPLIT_NO_EMPTY);
            $nombre = $partes[0] ?? 'SinNombre';
            $apellido1 = $partes[1] ?? 'SinApellido';
            $apellido2 = null;

            if (count($partes) > 2) {
                $apellido2 = implode(' ', array_slice($partes, 2));
            }

            $cedulaBase = trim($usuario['username'] ?? '');
            if ($cedulaBase === '' || strlen($cedulaBase) > 20) {
                $cedulaBase = 'TMP-' . $usuario_id;
            }
            if (strlen($cedulaBase) > 20) {
                $cedulaBase = substr($cedulaBase, 0, 20);
            }

            $cedula = $cedulaBase;
            $suffix = 1;
            while (true) {
                $check = $this->db->prepare("SELECT COUNT(*) FROM afiliados WHERE cedula = :cedula");
                $check->execute([':cedula' => $cedula]);
                if ((int)$check->fetchColumn() === 0) {
                    break;
                }
                $suffixStr = '-' . $suffix;
                $baseMax = 20 - strlen($suffixStr);
                $cedula = substr($cedulaBase, 0, $baseMax) . $suffixStr;
                $suffix++;
                if ($suffix > 99) {
                    return null;
                }
            }

            $sql = "INSERT INTO afiliados 
                        (cedula, nombre, apellido1, apellido2, correo, estado, fecha_creacion) 
                    VALUES 
                        (:cedula, :nombre, :apellido1, :apellido2, :correo, 'activo', NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cedula' => $cedula,
                ':nombre' => $nombre,
                ':apellido1' => $apellido1,
                ':apellido2' => $apellido2,
                ':correo' => $usuario['correo']
            ]);

            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creando afiliado básico en viáticos: " . $e->getMessage());
            return null;
        }
    }

    private function decodePayload($descripcion) {
        if (empty($descripcion)) {
            return [];
        }
        $data = json_decode($descripcion, true);
        return is_array($data) ? $data : [];
    }

    private function mapRowToViatico(array $row) {
        $payload = $this->decodePayload($row['descripcion_detallada'] ?? '');
        $estado = $payload['estado'] ?? 'Borrador';

        return [
            'id' => (int)($row['id'] ?? 0),
            'usuario_id' => isset($row['usuario_id']) ? (int) $row['usuario_id'] : null,
            'afiliado_id' => isset($row['afiliado_id']) ? (int) $row['afiliado_id'] : null,
            'consecutivo' => $payload['consecutivo'] ?? ($row['codigo_solicitud'] ?? ''),
            'estado' => $estado,
            'empleados' => $payload['empleados'] ?? null,
            'fecha_inicio' => $payload['fecha_inicio'] ?? null,
            'fecha_fin' => $payload['fecha_fin'] ?? null,
            'cantidad_dias' => (int)($payload['cantidad_dias'] ?? 0),
            'cantidad_desayuno' => (int)($payload['cantidad_desayuno'] ?? 0),
            'cantidad_almuerzo' => (int)($payload['cantidad_almuerzo'] ?? 0),
            'cantidad_cena' => (int)($payload['cantidad_cena'] ?? 0),
            'cantidad_transportes' => (int)($payload['cantidad_transportes'] ?? 0),
            'aplica_transporte' => (int)($payload['aplica_transporte'] ?? 0),
            'tipo_vehiculo' => $payload['tipo_vehiculo'] ?? null,
            'kilometraje' => (float)($payload['kilometraje'] ?? 0),
            'tarifa_km' => (float)($payload['tarifa_km'] ?? 0),
            'monto_transporte' => (float)($payload['monto_transporte'] ?? 0),
            'enlace_maps' => $payload['enlace_maps'] ?? null,
            'aplica_desayuno' => (int)($payload['aplica_desayuno'] ?? 0),
            'aplica_almuerzo' => (int)($payload['aplica_almuerzo'] ?? 0),
            'aplica_cena' => (int)($payload['aplica_cena'] ?? 0),
            'monto_alimentacion' => (float)($payload['monto_alimentacion'] ?? 0),
            'monto_hospedaje' => (float)($payload['monto_hospedaje'] ?? 0),
            'monto_gastos_menores' => (float)($payload['monto_gastos_menores'] ?? 0),
            'total_pagar' => (float)($payload['total_pagar'] ?? ($row['monto_solicitado'] ?? 0)),
            'archivo_comprobante' => $payload['archivo_comprobante'] ?? ($row['documentos_respaldo'] ?? null),
            'creado_en' => $row['fecha_creacion'] ?? ($payload['creado_en'] ?? null)
        ];
    }

    private function actualizarCodigoSolicitud($id, $codigo) {
        $sql = "UPDATE {$this->table} SET codigo_solicitud = :codigo WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':codigo' => $codigo,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error actualizando codigo_solicitud en viáticos: " . $e->getMessage());
        }
    }

    /**
     * Genera un consecutivo automático (Ej: VIAT-2026-0001)
     */
    public function generarConsecutivo() {
        $anio = date('Y');
        $maxNumero = 0;

        $storage = $this->listStorage();
        foreach ($storage as $record) {
            $cons = $record['consecutivo'] ?? '';
            if (preg_match('/^VIAT-(\d{4})-(\d{4})$/', $cons, $m) && $m[1] == $anio) {
                $num = (int)$m[2];
                if ($num > $maxNumero) {
                    $maxNumero = $num;
                }
            }
        }

        $dbMax = 0;
        $sql = "SELECT codigo_solicitud FROM {$this->table} WHERE codigo_solicitud LIKE :prefijo ORDER BY id DESC LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':prefijo' => "VIAT-$anio-%"]);
            $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ultimo && !empty($ultimo['codigo_solicitud'])) {
                $partes = explode('-', $ultimo['codigo_solicitud']);
                $dbMax = intval($partes[2]);
            }
        } catch (PDOException $e) {
            $dbMax = 0;
        }

        $maxNumero = max($maxNumero, $dbMax);
        $next = $maxNumero + 1;
        return "VIAT-$anio-" . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Registra una nueva solicitud de viáticos en estado Borrador
     */
    public function crearSolicitud($datos, $usuario_id = null) {
        $this->lastError = null;
        if ($this->storageMode) {
            return $this->crearSolicitudStorage($datos, $usuario_id);
        }

        $afiliado_id = $datos['afiliado_id'] ?? $this->resolverAfiliadoId($usuario_id);
        if (!$afiliado_id) {
            $this->lastError = "No se pudo resolver afiliado_id para solicitud de viáticos.";
            return false;
        }

        $consecutivo = $this->generarConsecutivo();
        $payload = [
            '__tipo' => 'viatico',
            'consecutivo' => $consecutivo,
            'estado' => 'Borrador',
            'empleados' => $datos['empleados'] ?? null,
            'fecha_inicio' => $datos['fecha_inicio'] ?? null,
            'fecha_fin' => $datos['fecha_fin'] ?? null,
            'cantidad_dias' => (int)($datos['cantidad_dias'] ?? 0),
            'cantidad_desayuno' => (int)($datos['cantidad_desayuno'] ?? 0),
            'cantidad_almuerzo' => (int)($datos['cantidad_almuerzo'] ?? 0),
            'cantidad_cena' => (int)($datos['cantidad_cena'] ?? 0),
            'cantidad_transportes' => (int)($datos['cantidad_transportes'] ?? 0),
            'aplica_transporte' => (int)($datos['aplica_transporte'] ?? 0),
            'tipo_vehiculo' => $datos['tipo_vehiculo'] ?? null,
            'kilometraje' => (float)($datos['kilometraje'] ?? 0),
            'tarifa_km' => (float)($datos['tarifa_km'] ?? 0),
            'monto_transporte' => (float)($datos['monto_transporte'] ?? 0),
            'enlace_maps' => $datos['enlace_maps'] ?? null,
            'aplica_desayuno' => (int)($datos['aplica_desayuno'] ?? 0),
            'aplica_almuerzo' => (int)($datos['aplica_almuerzo'] ?? 0),
            'aplica_cena' => (int)($datos['aplica_cena'] ?? 0),
            'monto_alimentacion' => (float)($datos['monto_alimentacion'] ?? 0),
            'monto_hospedaje' => (float)($datos['monto_hospedaje'] ?? 0),
            'monto_gastos_menores' => (float)($datos['monto_gastos_menores'] ?? 0),
            'total_pagar' => (float)($datos['total_pagar'] ?? 0),
            'archivo_comprobante' => $datos['archivo_comprobante'] ?? null
        ];

        $descripcion = json_encode($payload, JSON_UNESCAPED_UNICODE);
        if ($descripcion === false) {
            $descripcion = '{"__tipo":"viatico"}';
        }

        $sql = "INSERT INTO {$this->table} (
                    afiliado_id, monto_solicitado, motivo, descripcion_detallada, tipo_ayuda, documentos_respaldo, estado
                ) VALUES (
                    :afiliado_id, :monto_solicitado, :motivo, :descripcion_detallada, 'otra', :documentos_respaldo, 'pendiente'
                )";

        try {
            $stmt = $this->db->prepare($sql);
            $exito = $stmt->execute([
                ':afiliado_id' => $afiliado_id,
                ':monto_solicitado' => $datos['total_pagar'] ?? 0.00,
                ':motivo' => 'Viáticos',
                ':descripcion_detallada' => $descripcion,
                ':documentos_respaldo' => $datos['archivo_comprobante'] ?? null
            ]);

            if ($exito) {
                $nuevoId = $this->db->lastInsertId();
                if ($nuevoId) {
                    $this->actualizarCodigoSolicitud($nuevoId, $consecutivo);
                }
                return $nuevoId;
            }

            return false;

        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            error_log("Error guardando viático: " . $e->getMessage());
            return false;
        }
    }

    private function crearSolicitudStorage($datos, $usuario_id = null) {
        $consecutivo = $this->generarConsecutivo();
        $now = date('Y-m-d H:i:s');

        $record = [
            'id' => $this->generateStorageId(),
            'consecutivo' => $consecutivo,
            'estado' => 'Borrador',
            'empleados' => $datos['empleados'] ?? null,
            'fecha_inicio' => $datos['fecha_inicio'] ?? null,
            'fecha_fin' => $datos['fecha_fin'] ?? null,
            'cantidad_dias' => (int)($datos['cantidad_dias'] ?? 0),
            'cantidad_desayuno' => (int)($datos['cantidad_desayuno'] ?? 0),
            'cantidad_almuerzo' => (int)($datos['cantidad_almuerzo'] ?? 0),
            'cantidad_cena' => (int)($datos['cantidad_cena'] ?? 0),
            'cantidad_transportes' => (int)($datos['cantidad_transportes'] ?? 0),
            'aplica_transporte' => (int)($datos['aplica_transporte'] ?? 0),
            'tipo_vehiculo' => $datos['tipo_vehiculo'] ?? null,
            'kilometraje' => (float)($datos['kilometraje'] ?? 0),
            'tarifa_km' => (float)($datos['tarifa_km'] ?? 0),
            'monto_transporte' => (float)($datos['monto_transporte'] ?? 0),
            'enlace_maps' => $datos['enlace_maps'] ?? null,
            'aplica_desayuno' => (int)($datos['aplica_desayuno'] ?? 0),
            'aplica_almuerzo' => (int)($datos['aplica_almuerzo'] ?? 0),
            'aplica_cena' => (int)($datos['aplica_cena'] ?? 0),
            'monto_alimentacion' => (float)($datos['monto_alimentacion'] ?? 0),
            'monto_hospedaje' => (float)($datos['monto_hospedaje'] ?? 0),
            'monto_gastos_menores' => (float)($datos['monto_gastos_menores'] ?? 0),
            'total_pagar' => (float)($datos['total_pagar'] ?? 0),
            'archivo_comprobante' => $datos['archivo_comprobante'] ?? null,
            'usuario_id' => $usuario_id,
            'creado_en' => $now
        ];

        if (!$this->writeStorage($record)) {
            return false;
        }

        return $record['id'];
    }

    /**
     * Obtiene todos los viáticos registrados
     */
    public function obtenerTodos() {
        $storage = $this->listStorage();
        $storageById = [];
        foreach ($storage as $record) {
            if (!empty($record['id'])) {
                $storageById[$record['id']] = $record;
            }
        }

        $resultado = array_values($storageById);

        $sql = "SELECT s.id, s.afiliado_id, s.codigo_solicitud, s.descripcion_detallada, s.documentos_respaldo, s.estado, s.fecha_creacion, s.monto_solicitado,
                       u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                WHERE s.descripcion_detallada LIKE :marker
                ORDER BY s.id DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':marker' => '%' . self::VIATICO_MARK . '%']);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($storageById[$row['id']])) {
                    continue;
                }
                $resultado[] = $this->mapRowToViatico($row);
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo viáticos: " . $e->getMessage());
        }

        usort($resultado, function ($a, $b) {
            $fa = strtotime($a['creado_en'] ?? '1970-01-01');
            $fb = strtotime($b['creado_en'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $resultado;
    }

    /**
     * Obtiene los detalles de un viático específico por su ID
     */
    public function obtenerPorId($id) {
        $record = $this->readStorage($id);
        if ($record) {
            return $record;
        }

        $sql = "SELECT s.id, s.afiliado_id, s.codigo_solicitud, s.descripcion_detallada, s.documentos_respaldo, s.estado, s.fecha_creacion, s.monto_solicitado,
                       u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                WHERE s.id = :id
                AND s.descripcion_detallada LIKE :marker
                LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':marker' => '%' . self::VIATICO_MARK . '%'
            ]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return false;
            }
            return $this->mapRowToViatico($row);
        } catch (PDOException $e) {
            error_log("Error obteniendo viático por ID: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorUsuario($usuario_id) {
        $storage = $this->listStorage();
        $storageById = [];
        foreach ($storage as $record) {
            if (!empty($record['id']) && (int) ($record['usuario_id'] ?? 0) === (int) $usuario_id) {
                $storageById[$record['id']] = $record;
            }
        }

        $resultado = array_values($storageById);
        $afiliado_id = $this->resolverAfiliadoId($usuario_id);

        $sql = "SELECT s.id, s.afiliado_id, s.codigo_solicitud, s.descripcion_detallada, s.documentos_respaldo, s.estado, s.fecha_creacion, s.monto_solicitado,
                       u.id AS usuario_id
                FROM {$this->table} s
                INNER JOIN afiliados a ON s.afiliado_id = a.id
                LEFT JOIN usuarios u ON (u.correo = a.correo OR u.username = a.cedula)
                WHERE s.descripcion_detallada LIKE :marker
                  AND (u.id = :usuario_id OR s.afiliado_id = :afiliado_id)
                ORDER BY s.id DESC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':marker' => '%' . self::VIATICO_MARK . '%',
                ':usuario_id' => $usuario_id,
                ':afiliado_id' => $afiliado_id ?: 0
            ]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if (isset($storageById[$row['id']])) {
                    continue;
                }
                $resultado[] = $this->mapRowToViatico($row);
            }
        } catch (PDOException $e) {
            error_log("Error obteniendo viaticos por usuario: " . $e->getMessage());
        }

        usort($resultado, function ($a, $b) {
            $fa = strtotime($a['creado_en'] ?? '1970-01-01');
            $fb = strtotime($b['creado_en'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $resultado;
    }

    public function cambiarEstado($id, $nuevo_estado) {
        $this->lastError = null;

        $record = $this->readStorage($id);
        if ($record) {
            $record['estado'] = $nuevo_estado;
            $record['fecha_actualizacion'] = date('Y-m-d H:i:s');
            return $this->writeStorage($record);
        }

        try {
            $sql = "SELECT id, descripcion_detallada FROM {$this->table} WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                $this->lastError = 'Solicitud no encontrada.';
                return false;
            }
            $payload = $this->decodePayload($row['descripcion_detallada'] ?? '');
            $payload['estado'] = $nuevo_estado;
            $descripcion = json_encode($payload, JSON_UNESCAPED_UNICODE);
            if ($descripcion === false) {
                $this->lastError = 'No se pudo serializar el estado.';
                return false;
            }
            $update = $this->db->prepare("UPDATE {$this->table} SET descripcion_detallada = :desc WHERE id = :id");
            return $update->execute([
                ':desc' => $descripcion,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }
}
