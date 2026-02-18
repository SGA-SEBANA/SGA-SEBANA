<?php

namespace App\Modules\Bitacora\Models;
use App\Core\ModelBase;

class BitacoraModel extends ModelBase {

protected $table = "bitacora";



public function getBitacora()
{
    $sql= "SELECT
    accion,
    modulo,
    entidad,
    descripcion

    FROM Bitacora";


$stmt = $this->db->prepare($sql);
$stmt -> execute();
$results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

return $results;
}






}



