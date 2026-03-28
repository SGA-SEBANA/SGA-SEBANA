<?php
namespace App\Modules\Vacaciones\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class VacacionesModel extends ModelBase {
    
    private $lastError = '';

    public function __construct() {
        parent::__construct();
        // Le decimos a ModelBase con qué tabla va a trabajar mágicamente
        $this->table = 'solicitudes_vacaciones'; 
    }

    // HU-GCV-02 E1: Crear nueva solicitud
    public function crearSolicitud($usuario_id, $fecha_inicio, $fecha_fin, $motivo) {
        try {
            // Usamos el método create() de ModelBase
            return $this->create([
                'usuario_id' => $usuario_id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'motivo' => $motivo,
                'estado' => 'Pendiente'
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // Obtener todas las solicitudes de un usuario específico
    public function obtenerPorUsuario($usuario_id) {
        $sql = "SELECT * FROM {$this->table} WHERE usuario_id = :usuario_id ORDER BY fecha_creacion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener todas las solicitudes con info del usuario (Para el panel Admin/Jefatura)
    public function obtenerTodasConUsuario() {
        $sql = "SELECT v.*, u.nombre_completo, u.correo 
                FROM {$this->table} v 
                JOIN usuarios u ON v.usuario_id = u.id 
                ORDER BY v.fecha_creacion DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una solicitud específica por su ID con info del usuario
    public function obtenerDetallePorId($id) {
        $sql = "SELECT v.*, u.nombre_completo, u.correo 
                FROM {$this->table} v 
                JOIN usuarios u ON v.usuario_id = u.id 
                WHERE v.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // HU-GCV-01 E1 y E2, HU-GCV-02 E2: Cambiar el estado (Aprobar, Rechazar, Cancelar)
    public function cambiarEstado($id, $nuevo_estado) {
        try {
            // Usamos el método update() de ModelBase
            return $this->update($id, ['estado' => $nuevo_estado]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    // HU-GCV-02 E3: Reprogramar (Actualizar fechas y motivo, volver a Pendiente)
    public function reprogramarSolicitud($id, $fecha_inicio, $fecha_fin, $motivo) {
        try {
            // Usamos el método update() de ModelBase
            return $this->update($id, [
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin,
                'motivo' => $motivo,
                'estado' => 'Pendiente'
            ]);
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    public function getLastError() {
        return $this->lastError;
    }
}