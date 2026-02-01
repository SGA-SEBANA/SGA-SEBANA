<?php
namespace App\Modules\Carne\Models;

use App\Core\ModelBase;
use PDO;

class Carne extends ModelBase
{
    protected $table = 'carnets';

    /* ===============================
       Obtener carné por afiliado
    ================================ */
    public function getByAfiliadoId($afiliadoId)
    {
        $sql = "SELECT c.*, a.nombre, a.cedula, a.estado
                FROM {$this->table} c
                JOIN afiliados a ON c.afiliado_id = a.id
                WHERE c.afiliado_id = :afiliado_id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['afiliado_id' => $afiliadoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* ===============================
       Crear carné con numero_carnet
    ================================ */
    public function create($data)
    {
        // 1️⃣ Insertar carné base
        $sql = "INSERT INTO {$this->table}
                (afiliado_id, qr_code, version, estado, fecha_generacion)
                VALUES (:afiliado_id, :qr_code, :version, :estado, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        // 2️⃣ Obtener ID generado
        $carnetId = $this->db->lastInsertId();

        // 3️⃣ Generar número institucional
        $numeroCarnet = 'CAR-' . str_pad($carnetId, 6, '0', STR_PAD_LEFT);

        // 4️⃣ Guardar numero_carnet
        $sqlUpdate = "UPDATE {$this->table}
                      SET numero_carnet = :numero_carnet
                      WHERE id = :id";

        $stmtUpdate = $this->db->prepare($sqlUpdate);
        return $stmtUpdate->execute([
            'numero_carnet' => $numeroCarnet,
            'id' => $carnetId
        ]);
    }

    /* ===============================
       Actualizar QR y versión
    ================================ */
    public function updateQr($afiliadoId, $qrCode)
    {
        $sql = "UPDATE {$this->table}
                SET qr_code = :qr_code,
                    version = version + 1,
                    fecha_actualizacion = NOW()
                WHERE afiliado_id = :afiliado_id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'qr_code' => $qrCode,
            'afiliado_id' => $afiliadoId
        ]);
    }

    /* ===============================
       Obtener carné por QR
    ================================ */
    public function getByQrCode($qrCode)
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE qr_code = :qr_code
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['qr_code' => $qrCode]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
