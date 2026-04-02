<?php

namespace App\Modules\Visitas\Models;

use App\Core\ModelBase;

class VisitRequest extends ModelBase {

   protected $table = 'solicitudes_visitas_oficinas';

   public function getVisits($start = 0, $limit = 10)
   {
    $sql = "SELECT s.id,
        s.codigo_solicitud,
        s.afiliado_id,
        a.nombre AS afiliado_nombre,
        s.oficina_id,
        o.nombre AS oficina_nombre,
        s.numero_empleado, s.nombre_empleado, s.fecha_visita, s.hora_visita, s.motivo, s.tipo_visita, s.estado, s.fecha_reprogramada,
        s.hora_reprogramada, s.motivo_reprogramacion, s.motivo_cancelacion, s.motivo_rechazo, s.resultado_visita, s.aprobado_por,
        s.fecha_aprobacion, s.observaciones, s.fecha_creacion, s.fecha_actualizacion
    FROM solicitudes_visitas_oficinas s
    INNER JOIN afiliados a ON s.afiliado_id = a.id
    INNER JOIN oficinas o ON s.oficina_id = o.id
    ORDER BY s.fecha_creacion DESC
    LIMIT {$start}, {$limit}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

   public function getVisitsByAfiliado($afiliadoId, $start = 0, $limit = 10)
   {
    $sql = "SELECT s.id,
        s.codigo_solicitud,
        s.afiliado_id,
        a.nombre AS afiliado_nombre,
        s.oficina_id,
        o.nombre AS oficina_nombre,
        s.numero_empleado, s.nombre_empleado, s.fecha_visita, s.hora_visita, s.motivo, s.tipo_visita, s.estado, s.fecha_reprogramada,
        s.hora_reprogramada, s.motivo_reprogramacion, s.motivo_cancelacion, s.motivo_rechazo, s.resultado_visita, s.aprobado_por,
        s.fecha_aprobacion, s.observaciones, s.fecha_creacion, s.fecha_actualizacion
    FROM solicitudes_visitas_oficinas s
    INNER JOIN afiliados a ON s.afiliado_id = a.id
    INNER JOIN oficinas o ON s.oficina_id = o.id
    WHERE s.afiliado_id = :afiliado_id
    ORDER BY s.fecha_creacion DESC
    LIMIT {$start}, {$limit}";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':afiliado_id' => $afiliadoId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

public function getOffices() {
    $sql = 'SELECT id, nombre FROM oficinas ORDER BY nombre ASC';
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

public function getActiveAfiliados()
{
    $sql = "SELECT id, nombre_completo, cedula
            FROM afiliados
            WHERE estado = 'activo'
            ORDER BY nombre_completo ASC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getAfiliadoById($afiliadoId)
{
    $sql = "SELECT id, nombre_completo, cedula, correo
            FROM afiliados
            WHERE id = :id
            LIMIT 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([':id' => (int) $afiliadoId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
}

public function findAfiliadoIdByReferencia(?string $numeroEmpleado, ?string $nombreEmpleado): ?int
{
    $numeroEmpleado = trim((string) $numeroEmpleado);
    $nombreEmpleado = trim((string) $nombreEmpleado);

    if ($numeroEmpleado !== '') {
        $sql = "SELECT id
                FROM afiliados
                WHERE estado = 'activo'
                  AND (cedula = :cedula OR correo = :cedula)
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cedula' => $numeroEmpleado]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && !empty($row['id'])) {
            return (int) $row['id'];
        }
    }

    if ($nombreEmpleado !== '') {
        $sql = "SELECT id
                FROM afiliados
                WHERE estado = 'activo'
                  AND nombre_completo LIKE :nombre
                ORDER BY nombre_completo ASC
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nombre' => '%' . $nombreEmpleado . '%']);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && !empty($row['id'])) {
            return (int) $row['id'];
        }
    }

    return null;
}

public function createVisits(
    $afiliado_id,
    $oficina_id,
    $numero_empleado,
    $nombre_empleado,
    $fecha_visita,
    $hora_visita,
    $motivo,
    $tipo_visita,
    $estado,
    $fecha_reprogramada,
    $hora_reprogramada,
    $motivo_reprogramacion,
    $motivo_cancelacion,
    $motivo_rechazo,
    $resultado_visita,
    $aprobado_por,
    $fecha_aprobacion,
    $observaciones,
    $fecha_creacion,
    $fecha_actualizacion
){

   $sql = "INSERT INTO {$this->table}(
    afiliado_id,
    oficina_id,
    numero_empleado,
    nombre_empleado,
    fecha_visita,
    hora_visita,
    motivo,
    tipo_visita,
    estado,
    fecha_reprogramada,
    hora_reprogramada,
    motivo_reprogramacion,
    motivo_cancelacion,
    motivo_rechazo,
    resultado_visita,
    aprobado_por,
    fecha_aprobacion,
    observaciones,
    fecha_creacion,
    fecha_actualizacion
    ) VALUES (
    :afiliado_id,
    :oficina_id,
    :numero_empleado,
    :nombre_empleado,
    :fecha_visita,
    :hora_visita,
    :motivo,
    :tipo_visita,
    :estado,
    :fecha_reprogramada,
    :hora_reprogramada,
    :motivo_reprogramacion,
    :motivo_cancelacion,
    :motivo_rechazo,
    :resultado_visita,
    :aprobado_por,
    :fecha_aprobacion,
    :observaciones,
    :fecha_creacion,
    :fecha_actualizacion)";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':afiliado_id', $afiliado_id);
    $stmt->bindParam(':oficina_id', $oficina_id);
    $stmt->bindParam(':numero_empleado', $numero_empleado);
    $stmt->bindParam(':nombre_empleado', $nombre_empleado);
    $stmt->bindParam(':fecha_visita', $fecha_visita);
    $stmt->bindParam(':hora_visita', $hora_visita);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->bindParam(':tipo_visita', $tipo_visita);
    $stmt->bindParam(':estado', $estado);
    $stmt->bindParam(':fecha_reprogramada', $fecha_reprogramada);
    $stmt->bindParam(':hora_reprogramada', $hora_reprogramada);
    $stmt->bindParam(':motivo_reprogramacion', $motivo_reprogramacion);
    $stmt->bindParam(':motivo_cancelacion', $motivo_cancelacion);
    $stmt->bindParam(':motivo_rechazo', $motivo_rechazo);
    $stmt->bindParam(':resultado_visita', $resultado_visita);
    $stmt->bindParam(':aprobado_por', $aprobado_por);
    $stmt->bindParam(':fecha_aprobacion', $fecha_aprobacion);
    $stmt->bindParam(':observaciones', $observaciones);
    $stmt->bindParam(':fecha_creacion', $fecha_creacion);
    $stmt->bindParam(':fecha_actualizacion', $fecha_actualizacion);

    $stmt->execute();
    return $this->db->lastInsertId();
   }

public function rescheduleVisit($id, $fecha_reprogramada, $hora_reprogramada, $motivo_reprogramacion)
{
    $sql = "UPDATE {$this->table}
            SET fecha_reprogramada = :fecha_reprogramada,
                hora_reprogramada = :hora_reprogramada,
                motivo_reprogramacion = :motivo_reprogramacion,
                estado = 'pendiente',
                fecha_actualizacion = NOW()
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':fecha_reprogramada', $fecha_reprogramada);
    $stmt->bindParam(':hora_reprogramada', $hora_reprogramada);
    $stmt->bindParam(':motivo_reprogramacion', $motivo_reprogramacion);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}

public function getVisitById($id) {
    $sql = "SELECT * FROM {$this->table} WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function acceptVisit($id){

$sql= "UPDATE solicitudes_visitas_oficinas
SET estado = 'aprobada',
fecha_aprobacion = NOW()
WHERE id = :id";

$stmt = $this->db->prepare($sql);
$stmt->execute([':id' => $id]);

}

public function updateEstado($id, $estado)
{
    $sql = "UPDATE solicitudes_visitas_oficinas
            SET estado = :estado,
                fecha_actualizacion = NOW()
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':estado' => $estado,
        ':id' => $id
    ]);
}

public function getCalendarEvents()
{
    $sql = "SELECT id, fecha_visita, hora_visita, estado
            FROM solicitudes_visitas_oficinas
            WHERE estado = 'aprobada'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getApprovedVisits()
{
    $sql = "SELECT id, nombre_empleado, fecha_visita, hora_visita
            FROM solicitudes_visitas_oficinas
            WHERE estado = 'aprobada'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getUpcomingVisits()
{
    $sql = "SELECT nombre_empleado, fecha_visita, hora_visita
            FROM solicitudes_visitas_oficinas
            WHERE estado = 'aprobada'
            AND fecha_visita >= CURDATE()
            ORDER BY fecha_visita ASC
            LIMIT 5";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function countVisits($filtros = [])
{
    $sql = "SELECT COUNT(*) as total FROM {$this->table}";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return (int) $stmt->fetchColumn();
}

public function countVisitsByAfiliado($afiliadoId)
{
    $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE afiliado_id = :afiliado_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':afiliado_id' => $afiliadoId]);
    return (int) $stmt->fetchColumn();
}

public function resolveAfiliadoIdByUser($usuarioId)
{
    if (empty($usuarioId)) {
        return null;
    }

    $stmt = $this->db->prepare('SELECT id FROM afiliados WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $usuarioId]);
    $directo = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($directo && !empty($directo['id'])) {
        return (int) $directo['id'];
    }

    $stmt = $this->db->prepare(
        'SELECT a.id
         FROM usuarios u
         INNER JOIN afiliados a ON a.correo = u.correo
         WHERE u.id = :usuario_id
         LIMIT 1'
    );
    $stmt->execute([':usuario_id' => $usuarioId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($row && !empty($row['id'])) {
        return (int) $row['id'];
    }

    $stmt = $this->db->prepare(
        'SELECT a.id
         FROM usuarios u
         INNER JOIN afiliados a ON (a.cedula = u.username OR a.correo = u.username)
         WHERE u.id = :usuario_id
         LIMIT 1'
    );
    $stmt->execute([':usuario_id' => $usuarioId]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($row && !empty($row['id'])) {
        return (int) $row['id'];
    }

    return null;
}

}
