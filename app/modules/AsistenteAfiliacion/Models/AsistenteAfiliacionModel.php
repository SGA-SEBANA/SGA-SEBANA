<?php
namespace App\Modules\AsistenteAfiliacion\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class AsistenteAfiliacionModel extends ModelBase
{
    private $storageBase;
    private $requestsDir;
    private $pdfDir;
    private $signedDir;
    private $lastError = '';

    public function __construct()
    {
        parent::__construct();
        $this->storageBase = BASE_PATH . '/storage/asistente_afiliacion';
        $this->requestsDir = $this->storageBase . '/requests';
        $this->pdfDir = $this->storageBase . '/pdfs';
        $this->signedDir = $this->storageBase . '/firmados';
    }

    public function getLastError()
    {
        return $this->lastError;
    }

    public function ensureStorageDirs()
    {
        foreach ([$this->storageBase, $this->requestsDir, $this->pdfDir, $this->signedDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    private function requestPath($id)
    {
        return $this->requestsDir . '/' . $id . '.json';
    }

    private function generateId()
    {
        $this->ensureStorageDirs();
        $id = (int) round(microtime(true) * 1000);
        while (file_exists($this->requestPath($id))) {
            $id++;
        }
        return $id;
    }

    public function normalizeCedula($cedula)
    {
        return preg_replace('/[^0-9A-Za-z]/', '', trim((string) $cedula)) ?? '';
    }

    public function normalizePhone($phone)
    {
        $phone = trim((string) $phone);
        $phone = str_replace([' ', '-', '(', ')'], '', $phone);
        return $phone;
    }

    public function normalizeEmail($correo)
    {
        return strtolower(trim((string) $correo));
    }

    public function isTipoUsuarioPermitido($tipo)
    {
        $tipo = strtolower(trim((string) $tipo));
        return in_array($tipo, ['activo', 'jubilado'], true);
    }

    public function getOficinasDisponibles()
    {
        $sql = "SELECT id, codigo, nombre
                FROM oficinas
                WHERE activo = 1
                ORDER BY nombre ASC";

        try {
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getCategoriasAfiliacion()
    {
        $sql = "SELECT id, nombre
                FROM categorias
                WHERE estado = 'activo' AND tipo IN ('afiliado', 'general')
                ORDER BY nombre ASC";

        try {
            return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return [];
        }
    }

    public function getNombreOficinaById($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("SELECT nombre FROM oficinas WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['nombre'] ?? null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function read($id)
    {
        $path = $this->requestPath($id);
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

    public function write(array $record)
    {
        $this->ensureStorageDirs();

        if (empty($record['id'])) {
            $this->lastError = 'Registro invalido.';
            return false;
        }

        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if ($json === false) {
            $this->lastError = 'No se pudo serializar el registro.';
            return false;
        }

        $ok = file_put_contents($this->requestPath($record['id']), $json) !== false;
        if (!$ok) {
            $this->lastError = 'No se pudo guardar la solicitud.';
        }

        return $ok;
    }

    public function listAll()
    {
        $this->ensureStorageDirs();
        $rows = [];
        $files = glob($this->requestsDir . '/*.json');
        if (!$files) {
            return $rows;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }
            $row = json_decode($content, true);
            if (is_array($row)) {
                $rows[] = $row;
            }
        }

        usort($rows, function ($a, $b) {
            $fa = strtotime($a['fecha_actualizacion'] ?? $a['fecha_creacion'] ?? '1970-01-01');
            $fb = strtotime($b['fecha_actualizacion'] ?? $b['fecha_creacion'] ?? '1970-01-01');
            return $fb <=> $fa;
        });

        return $rows;
    }

    public function cedulaExisteEnAfiliados($cedula)
    {
        $cedula = trim((string) $cedula);
        $cedulaNormal = $this->normalizeCedula($cedula);

        if ($cedulaNormal === '') {
            return false;
        }

        try {
            $sql = "SELECT COUNT(*) 
                    FROM afiliados 
                    WHERE cedula = :cedula 
                       OR REPLACE(REPLACE(cedula, '-', ''), ' ', '') = :cedula_normal";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':cedula' => $cedula,
                ':cedula_normal' => $cedulaNormal
            ]);

            return ((int) $stmt->fetchColumn()) > 0;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function cedulaExisteEnSolicitudes($cedula, $excludeId = null)
    {
        $cedulaNormal = $this->normalizeCedula($cedula);
        if ($cedulaNormal === '') {
            return false;
        }

        foreach ($this->listAll() as $row) {
            if (!empty($excludeId) && (string) ($row['id'] ?? '') === (string) $excludeId) {
                continue;
            }

            $actual = $this->normalizeCedula($row['cedula'] ?? '');
            if ($actual !== '' && $actual === $cedulaNormal) {
                return true;
            }
        }

        return false;
    }

    public function cedulaDuplicada($cedula, $excludeId = null)
    {
        if ($this->cedulaExisteEnAfiliados($cedula)) {
            return true;
        }

        return $this->cedulaExisteEnSolicitudes($cedula, $excludeId);
    }

    public function correoExisteEnAfiliados($correo)
    {
        $correo = $this->normalizeEmail($correo);
        if ($correo === '') {
            return false;
        }

        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM afiliados WHERE LOWER(correo) = :correo");
            $stmt->execute([':correo' => $correo]);
            return ((int) $stmt->fetchColumn()) > 0;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function correoExisteEnSolicitudes($correo, $excludeId = null)
    {
        $correo = $this->normalizeEmail($correo);
        if ($correo === '') {
            return false;
        }

        foreach ($this->listAll() as $row) {
            if (!empty($excludeId) && (string) ($row['id'] ?? '') === (string) $excludeId) {
                continue;
            }

            $actual = $this->normalizeEmail($row['correo'] ?? '');
            if ($actual !== '' && $actual === $correo) {
                return true;
            }
        }

        return false;
    }

    public function correoDuplicado($correo, $excludeId = null)
    {
        if ($this->correoExisteEnAfiliados($correo)) {
            return true;
        }

        return $this->correoExisteEnSolicitudes($correo, $excludeId);
    }

    public function findDraftByCedula($cedula)
    {
        $cedulaNormal = $this->normalizeCedula($cedula);
        if ($cedulaNormal === '') {
            return null;
        }

        foreach ($this->listAll() as $row) {
            $estado = strtolower(trim((string) ($row['estado'] ?? '')));
            if (!in_array($estado, ['borrador', 'pdf_generado'], true)) {
                continue;
            }

            if ($this->normalizeCedula($row['cedula'] ?? '') === $cedulaNormal) {
                return $row;
            }
        }

        return null;
    }

    public function saveDraft(array $data, $draftId = null)
    {
        $this->lastError = '';

        $existing = null;
        if (!empty($draftId)) {
            $existing = $this->read($draftId);
        }

        if (!$existing) {
            $existing = $this->findDraftByCedula($data['cedula'] ?? '');
        }

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $record = $existing;
            $record['fecha_actualizacion'] = $now;
        } else {
            $record = [
                'id' => $this->generateId(),
                'estado' => 'borrador',
                'fecha_creacion' => $now
            ];
            $record['fecha_actualizacion'] = $now;
        }

        $record['tipo_usuario'] = $data['tipo_usuario'];
        $record['cedula'] = $data['cedula'];
        $record['nombre'] = $data['nombre'];
        $record['apellido1'] = $data['apellido1'];
        $record['apellido2'] = $data['apellido2'];
        $record['correo'] = $data['correo'];
        $record['fecha_nacimiento'] = $data['fecha_nacimiento'];
        $record['numero_empleado'] = $data['numero_empleado'];
        $record['oficina_bncr'] = $data['oficina_bncr'];
        $record['oficina_id'] = !empty($data['oficina_id']) ? (int) $data['oficina_id'] : null;
        $record['categoria_id'] = !empty($data['categoria_id']) ? (int) $data['categoria_id'] : null;
        $record['departamento'] = $data['departamento'];
        $record['puesto'] = $data['puesto'];
        $record['fecha_ingreso_bncr'] = $data['fecha_ingreso_bncr'];
        $record['fecha_jubilacion'] = $data['fecha_jubilacion'];
        $record['celular'] = $data['celular'];
        $record['acepta_deduccion'] = (int) $data['acepta_deduccion'];
        $record['acepta_estatuto'] = (int) $data['acepta_estatuto'];
        $record['observaciones'] = $data['observaciones'];

        if (!isset($record['pdf_generado_path'])) {
            $record['pdf_generado_path'] = null;
        }
        if (!isset($record['pdf_firmado_path'])) {
            $record['pdf_firmado_path'] = null;
        }
        if (!isset($record['fecha_envio'])) {
            $record['fecha_envio'] = null;
        }
        if (!isset($record['revisado_por'])) {
            $record['revisado_por'] = null;
        }
        if (!isset($record['observaciones_admin'])) {
            $record['observaciones_admin'] = null;
        }

        if (!$this->write($record)) {
            return false;
        }

        return $record;
    }

    public function saveGeneratedPdf($id, $binary)
    {
        $this->ensureStorageDirs();
        $name = 'solicitud_' . $id . '.pdf';
        $fullPath = $this->pdfDir . '/' . $name;
        $ok = file_put_contents($fullPath, $binary) !== false;

        if (!$ok) {
            $this->lastError = 'No se pudo guardar el PDF generado.';
            return false;
        }

        $relativePath = 'storage/asistente_afiliacion/pdfs/' . $name;
        $record = $this->read($id);
        if (!$record) {
            $this->lastError = 'No se encontro el borrador.';
            return false;
        }

        $record['pdf_generado_path'] = $relativePath;
        $record['estado'] = 'pdf_generado';
        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');

        if (!$this->write($record)) {
            return false;
        }

        return $relativePath;
    }

    public function saveSignedPdf($id, $tmpPath, $originalName)
    {
        $this->ensureStorageDirs();
        $safeName = preg_replace('/[^A-Za-z0-9_.-]/', '_', (string) $originalName) ?: 'documento_firmado.pdf';
        $name = 'firmado_' . $id . '_' . time() . '_' . $safeName;
        $fullPath = $this->signedDir . '/' . $name;

        if (!move_uploaded_file($tmpPath, $fullPath)) {
            $this->lastError = 'No se pudo almacenar el PDF firmado.';
            return false;
        }

        return 'storage/asistente_afiliacion/firmados/' . $name;
    }

    public function submitForApproval($id, array $data, $signedPath)
    {
        $record = $this->read($id);
        if (!$record) {
            $this->lastError = 'No se encontro la solicitud.';
            return false;
        }

        $record['tipo_usuario'] = $data['tipo_usuario'];
        $record['cedula'] = $data['cedula'];
        $record['nombre'] = $data['nombre'];
        $record['apellido1'] = $data['apellido1'];
        $record['apellido2'] = $data['apellido2'];
        $record['correo'] = $data['correo'];
        $record['fecha_nacimiento'] = $data['fecha_nacimiento'];
        $record['numero_empleado'] = $data['numero_empleado'];
        $record['oficina_bncr'] = $data['oficina_bncr'];
        $record['oficina_id'] = !empty($data['oficina_id']) ? (int) $data['oficina_id'] : null;
        $record['categoria_id'] = !empty($data['categoria_id']) ? (int) $data['categoria_id'] : null;
        $record['departamento'] = $data['departamento'];
        $record['puesto'] = $data['puesto'];
        $record['fecha_ingreso_bncr'] = $data['fecha_ingreso_bncr'];
        $record['fecha_jubilacion'] = $data['fecha_jubilacion'];
        $record['celular'] = $data['celular'];
        $record['acepta_deduccion'] = (int) $data['acepta_deduccion'];
        $record['acepta_estatuto'] = (int) $data['acepta_estatuto'];
        $record['observaciones'] = $data['observaciones'];

        $record['pdf_firmado_path'] = $signedPath;
        $record['estado'] = 'enviada_aprobacion';
        $record['fecha_envio'] = date('Y-m-d H:i:s');
        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');

        return $this->write($record);
    }

    public function updateStatus($id, $estado, $observaciones, $reviewerUserId = null)
    {
        $record = $this->read($id);
        if (!$record) {
            $this->lastError = 'No se encontro la solicitud.';
            return false;
        }

        $estado = strtolower(trim((string) $estado));
        $permitidos = ['enviada_aprobacion', 'aprobada', 'rechazada'];
        if (!in_array($estado, $permitidos, true)) {
            $this->lastError = 'Estado no permitido.';
            return false;
        }

        $record['estado'] = $estado;
        $record['observaciones_admin'] = trim((string) $observaciones);
        $record['revisado_por'] = $reviewerUserId;
        $record['fecha_revision'] = date('Y-m-d H:i:s');
        $record['fecha_actualizacion'] = date('Y-m-d H:i:s');

        return $this->write($record);
    }

    public function buildAbsolutePath($relativePath)
    {
        if (empty($relativePath)) {
            return null;
        }

        $relativePath = ltrim((string) $relativePath, '/\\');
        $candidate = BASE_PATH . DIRECTORY_SEPARATOR . $relativePath;
        if (!file_exists($candidate)) {
            return null;
        }

        $realBase = realpath(BASE_PATH);
        $realFile = realpath($candidate);
        if (!$realBase || !$realFile || strpos($realFile, $realBase) !== 0) {
            return null;
        }

        return $realFile;
    }
}
