<?php

namespace App\Modules\Visitas\Models;

use App\Core\ModelBase;
use PDO;

class Notification extends ModelBase {

    protected $table = "notificaciones";

    public function __construct() {
        parent::__construct();
        $this->table = "notificaciones";
    }

    private function normalizeCategoria($categoria) {
        $allowed = [
            'afiliados',
            'solicitudes',
            'casos',
            'vacaciones',
            'ayuda_economica',
            'visitas',
            'sistema',
            'seguridad',
            'aprobaciones'
        ];

        $categoria = strtolower(trim((string)$categoria));
        return in_array($categoria, $allowed, true) ? $categoria : 'sistema';
    }

    private function normalizePrioridad($prioridad) {
        $allowed = ['baja', 'normal', 'alta', 'urgente'];
        $prioridad = strtolower(trim((string)$prioridad));
        return in_array($prioridad, $allowed, true) ? $prioridad : 'normal';
    }

    /**
     * HU-NO-01, 02, 03, 04 - Escenario 1: GeneraciÃ³n automÃ¡tica
     * Crea una notificaciÃ³n con todos los metadatos necesarios.
     */
    public function createNotification(
        $usuario_id,
        $tipo,
        $categoria,
        $titulo,
        $mensaje,
        $entidad_tipo,
        $entidad_id,
        $url_accion = null,
        $prioridad = 'normal'
    ) {
        $categoria = $this->normalizeCategoria($categoria);
        $prioridad = $this->normalizePrioridad($prioridad);

        $sql = "INSERT INTO {$this->table} (
            usuario_id, tipo, categoria, titulo, mensaje,
            entidad_tipo, entidad_id, url_accion,
            leida, archivada, prioridad, requiere_accion,
            accion_completada, fecha_creacion
        ) VALUES (
            :usuario_id, :tipo, :categoria, :titulo, :mensaje,
            :entidad_tipo, :entidad_id, :url_accion,
            0, 0, :prioridad, 1, 0, NOW()
        )";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id'   => $usuario_id,
            ':tipo'         => $tipo,
            ':categoria'    => $categoria,
            ':titulo'       => $titulo,
            ':mensaje'      => $mensaje,
            ':entidad_tipo' => $entidad_tipo,
            ':entidad_id'   => $entidad_id,
            ':url_accion'   => $url_accion,
            ':prioridad'    => $prioridad
        ]);
    }

    /**
     * Obtiene notificaciones activas para la campana del header
     */
    public function getUnreadByUser($usuario_id) {
        $sql = "SELECT * FROM {$this->table}
                WHERE usuario_id = :usuario_id
                AND leida = 0
                AND archivada = 0
                ORDER BY fecha_creacion DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total para el badge rojo de la campana
     */
    public function countUnread($usuario_id) {
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE usuario_id = :usuario_id
                AND leida = 0
                AND archivada = 0";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['usuario_id' => $usuario_id]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Marcar como leÃ­da tras revisar (HU-NO-01 E2)
     */
    public function markAsRead($id) {
        $sql = "UPDATE {$this->table}
                SET leida = 1, fecha_lectura = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function markAsReadForUser($id, $usuario_id) {
        $sql = "UPDATE {$this->table}
                SET leida = 1, fecha_lectura = NOW()
                WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'usuario_id' => $usuario_id
        ]);
    }

    /**
     * Marcar todas como leÃ­das (Limpieza rÃ¡pida)
     */
    public function markAllAsReadByUser($usuario_id) {
        $sql = "UPDATE {$this->table}
                SET leida = 1, fecha_lectura = NOW()
                WHERE usuario_id = :usuario_id
                AND leida = 0";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['usuario_id' => $usuario_id]);
    }

    /**
     * HU-NO-01, 03, 04 - Escenario 2: Eliminar/Gestionar notificaciÃ³n
     * Archiva la notificaciÃ³n para que deje de ser visible pero se mantenga el log.
     */
    public function archive($id) {
        $sql = "UPDATE {$this->table}
                SET archivada = 1
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function archiveForUser($id, $usuario_id) {
        $sql = "UPDATE {$this->table}
                SET archivada = 1
                WHERE id = :id AND usuario_id = :usuario_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'usuario_id' => $usuario_id
        ]);
    }
}
