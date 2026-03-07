<?php
namespace App\Modules\Ayudas\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class AyudaEconomicaModel extends ModelBase {
    
    protected $table = 'ayudas_economicas';
    protected $table_evidencias = 'ayudas_evidencias';

    public function __construct() {
        parent::__construct();
    }

    public function crearSolicitud($usuario_id, $motivo, $monto_solicitado) {
        $sql = "INSERT INTO {$this->table} (usuario_id, motivo, monto_solicitado, estado) 
                VALUES (:usuario_id, :motivo, :monto_solicitado, 'Pendiente')";
        
        try {
            $stmt = $this->db->prepare($sql);
            $exito = $stmt->execute([
                ':usuario_id' => $usuario_id,
                ':motivo' => $motivo,
                ':monto_solicitado' => $monto_solicitado
            ]);
            
            return $exito ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Error creando solicitud de ayuda: " . $e->getMessage());
            return false;
        }
    }

    /**
     * HU-SAEC-1 Escenario 2: Registro de solicitud de cancelación por el usuario.
     */
    public function registrarCancelacionUsuario($id, $motivo_cancelacion) {
        $sql = "UPDATE {$this->table} SET 
                estado = 'Cancelación Solicitada', 
                motivo_cancelacion = :motivo 
                WHERE id = :id AND estado = 'Pendiente'";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':motivo' => $motivo_cancelacion,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error registrando cancelación de usuario: " . $e->getMessage());
            return false;
        }
    }

    public function guardarEvidencia($ayuda_id, $usuario_id, $nombre_archivo, $ruta_archivo, $estado_solicitud) {
        $sql = "INSERT INTO {$this->table_evidencias} 
                (ayuda_id, usuario_id, nombre_archivo, ruta_archivo, estado_solicitud_al_subir) 
                VALUES (:ayuda_id, :usuario_id, :nombre_archivo, :ruta_archivo, :estado)";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':ayuda_id' => $ayuda_id,
                ':usuario_id' => $usuario_id,
                ':nombre_archivo' => $nombre_archivo,
                ':ruta_archivo' => $ruta_archivo,
                ':estado' => $estado_solicitud
            ]);
        } catch (PDOException $e) {
            error_log("Error guardando evidencia: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerPorId($id) {
        $sql = "SELECT a.*, u.nombre_completo, u.correo 
                FROM {$this->table} a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo ayuda por ID: " . $e->getMessage());
            return false;
        }
    }

    public function obtenerEvidencias($ayuda_id) {
        $sql = "SELECT e.*, u.nombre_completo 
                FROM {$this->table_evidencias} e
                INNER JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.ayuda_id = :ayuda_id 
                ORDER BY e.fecha_carga ASC";
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
        $sql = "SELECT a.*, u.nombre_completo 
                FROM {$this->table} a
                INNER JOIN usuarios u ON a.usuario_id = u.id
                ORDER BY a.fecha_solicitud DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo todas las ayudas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * HU-SAEC-5: Obtener correos de administradores para notificaciones de nueva evidencia.
     */
    public function obtenerCorreosAdministradores() {
        $sql = "SELECT correo FROM usuarios WHERE rol = 'Administrador'";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            error_log("Error obteniendo correos de administradores: " . $e->getMessage());
            return [];
        }
    }

    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE {$this->table} SET estado = :estado WHERE id = :id";
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':estado' => $nuevo_estado,
                ':id' => $id
            ]);
        } catch (PDOException $e) {
            error_log("Error cambiando estado: " . $e->getMessage());
            return false;
        }
    }
}