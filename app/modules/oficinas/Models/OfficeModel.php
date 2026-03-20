<?php

namespace App\Modules\Oficinas\Models;

use App\Core\ModelBase;

class OfficeModel extends ModelBase
{
    protected $table = "oficinas";

    public function getAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY nombre ASC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function createOffice($data)
    {
        $sql = "INSERT INTO {$this->table} (
            codigo, nombre, direccion, provincia, canton, distrito, telefono, correo,
            horario_atencion, responsable, coordenadas_gps, observaciones, estado
        ) VALUES (
            :codigo, :nombre, :direccion, :provincia, :canton, :distrito, :telefono, :correo,
            :horario_atencion, :responsable, :coordenadas_gps, :observaciones, 'activo'
        )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $this->db->lastInsertId();
    }

    public function updateOffice($id, $data)
    {
        $fields = '';
        foreach ($data as $key => $value) {
            $fields .= "{$key} = :{$key}, ";
        }
        $fields = rtrim($fields, ', ');
        $data['id'] = $id;

        $sql = "UPDATE {$this->table} SET {$fields} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }
public function toggleStatus($id)
{
    // Obtener el valor actual
    $stmt = $this->db->prepare("SELECT activo FROM {$this->table} WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $office = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$office) return false;

    // Cambiar 1 <-> 0
    $newStatus = $office['activo'] == 1 ? 0 : 1;

    $stmt = $this->db->prepare("UPDATE {$this->table} SET activo = :activo WHERE id = :id");
    return $stmt->execute(['activo' => $newStatus, 'id' => $id]);
}
    public function deleteOffice($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}