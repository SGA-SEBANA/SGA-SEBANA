<?php

namespace App\Modules\Bitacora\Models;
use App\Core\ModelBase;


class BitacoraModel extends ModelBase {

protected $table = "bitacora";




public function getBitacora($filtros = [])
{
    $sql = "SELECT
        id,
        usuario_id,
        accion,
        modulo,
        entidad,
        entidad_id,
        descripcion,
        datos_anteriores,
        datos_nuevos,
        user_agent,
        metodo_http,
        resultado,
        codigo_error,
        mensaje_error,
        url_accedida,
        duracion_ms
    FROM {$this->table}
    WHERE 1=1
    ";

    $params = [];

    
    if (!empty($filtros['busqueda'])) {
        $sql .= " AND (
            accion LIKE :b OR
            modulo LIKE :b OR
            entidad LIKE :b OR
            descripcion LIKE :b
        )";
        $params['b'] = "%" . $filtros['busqueda'] . "%";
    }

 
    if (!empty($filtros['modulo'])) {
        $sql .= " AND modulo = :modulo";
        $params['modulo'] = $filtros['modulo'];
    }

 
    if (!empty($filtros['accion'])) {
        $sql .= " AND accion = :accion";
        $params['accion'] = $filtros['accion'];
    }

   
    if (!empty($filtros['resultado'])) {
        $sql .= " AND resultado = :resultado";
        $params['resultado'] = $filtros['resultado'];
    }
   
    if (!empty($filtros['fecha'])) {
        $sql .= " AND DATE(fecha_creacion) = :fecha";
        $params['fecha'] = $filtros['fecha'];
    }

    $sql .= " ORDER BY fecha_creacion DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}





public function getModulos()
{
    $sql = "SELECT DISTINCT modulo FROM {$this->table} ORDER BY modulo";
    return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
}

public function getAcciones()
{
    $sql = "SELECT DISTINCT accion FROM {$this->table} ORDER BY accion";
    return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
}

public function getResultados()
{
    $sql = "SELECT DISTINCT resultado FROM {$this->table} ORDER BY resultado";
    return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
}



public function getBitacoraById($id){

    $sql = "SELECT 
        id,
        accion,
        modulo,
        entidad,
        descripcion,
        datos_anteriores,
        datos_nuevos,
        ip_address,
        resultado, 
        codigo_error,
        fecha_creacion,
        mensaje_error 
    FROM {$this->table}
    WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(\PDO::FETCH_ASSOC);
}






}