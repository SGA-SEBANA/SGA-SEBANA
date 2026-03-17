<?php

namespace App\Modules\Visitas\Models;

use App\Core\ModelBase;

class Notification extends ModelBase {

    protected $table = "notificaciones";

    public function createNotification(
        $usuario_id,
        $tipo,
        $categoria,
        $titulo,
        $mensaje,
        $entidad_tipo,
        $entidad_id,
        $url_accion = null
    ){
        $sql = "INSERT INTO {$this->table} (
            usuario_id,
            tipo,
            categoria,
            titulo,
            mensaje,
            entidad_tipo,
            entidad_id,
            url_accion,
            leida,
            archivada,
            prioridad,
            requiere_accion,
            accion_completada,
            fecha_creacion
        ) VALUES (
            :usuario_id,
            :tipo,
            :categoria,
            :titulo,
            :mensaje,
            :entidad_tipo,
            :entidad_id,
            :url_accion,
            0,
            0,
            'media',
            1,
            0,
            NOW()
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':tipo' => $tipo,
            ':categoria' => $categoria,
            ':titulo' => $titulo,
            ':mensaje' => $mensaje,
            ':entidad_tipo' => $entidad_tipo,
            ':entidad_id' => $entidad_id,
            ':url_accion' => $url_accion
        ]);
    }


    public function getUnreadByUser($usuario_id)
{
    $sql = "SELECT * FROM {$this->table}
            WHERE usuario_id = :usuario_id
            AND leida = 0
            AND archivada = 0
            ORDER BY fecha_creacion DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['usuario_id' => $usuario_id]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countUnread($usuario_id)
{
    $sql = "SELECT COUNT(*) FROM {$this->table}
            WHERE usuario_id = :usuario_id
            AND leida = 0
            AND archivada = 0";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['usuario_id' => $usuario_id]);

    return (int) $stmt->fetchColumn();
}

public function markAsRead($id)
{
    $sql = "UPDATE {$this->table}
            SET leida = 1, fecha_lectura = NOW()
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

public function markAllAsReadByUser($usuario_id)
{
    $sql = "UPDATE {$this->table}
            SET leida = 1, fecha_lectura = NOW()
            WHERE usuario_id = :usuario_id
            AND leida = 0";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute(['usuario_id' => $usuario_id]);
}
}