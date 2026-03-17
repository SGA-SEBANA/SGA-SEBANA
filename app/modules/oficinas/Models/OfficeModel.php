<?php
namespace App\Modules\oficinas\Models;
use App\Core\ModelBase;

class OfficeModel extends ModelBase{

 protected $table = "oficinas";


 public function getOffice(){

   $sql = "SELECT 
    id,
    codigo,
    nombre,
    direccion,
    provincia,
    canton,
    distrito,
    telefono,
    correo,
    horario_atencion,
    responsable,
    activo,
    coordenadas_gps,
    observaciones,
    fecha_creacion,
    fecha_actualizacion
    FROM oficinas
    WHERE activo = 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
    return $results;

 }



public function createOffice($id, $codigo, $nombre, $direccion, $provincia, $canton, $distrito, $telefono, $correo, $horario_atencion, $responsable, $activo, $coordenadas_gps, $observaciones, $fecha_creacion, $fecha_actualizacion){
    
        $sql = "INSERT INTO {$this->table} (
            codigo,
            nombre,
            direccion,
            provincia,
            canton,
            distrito,
            telefono,
            correo,
            horario_atencion,
            responsable,
            activo,
            coordenadas_gps,
            observaciones,
            fecha_creacion,
            fecha_actualizacion
        ) VALUES (
            :codigo,
            :nombre,
            :direccion,
            :provincia,
            :canton,
            :distrito,
            :telefono,
            :correo,
            :horario_atencion,
            :responsable,
            :activo,
            :coordenadas_gps,
            :observaciones,
            NOW(),
            NOW())";

            $stmt->bindParam(':codigo', $codigo);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':direccion', $direccion);
            $stmt->bindParam(':provincia', $provincia);
            $stmt->bindParam(':canton', $canton);
            $stmt->bindParam(':distrito', $distrito);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':horario_atencion', $horario_atencion);
            $stmt->bindParam(':responsable', $responsable);
            $stmt->bindParam(':activo', $activo);
            $stmt->bindParam(':coordenadas_gps', $coordenadas_gps);
            $stmt->bindParam(':observaciones', $observaciones);

            $stmt->execute();

}


 
public function editOffice($id, $codigo, $nombre, $direccion, $provincia, $canton, $distrito, $telefono, $correo, $horario_atencion, $responsable, $activo, $coordenadas_gps, $observaciones){
    $sql = "UPDATE oficinas SET
    codigo = :codigo,
    nombre = :nombre,
    direccion = :direccion,
    provincia = :provincia,
    canton = :canton,
    distrito = :distrito,
    telefono = :telefono,
    correo = :correo,
    horario_atencion = :horario_atencion,
    responsable = :responsable,
    activo = :activo,
    coordenadas_gps = :coordenadas_gps,
    observaciones = :observaciones,
    fecha_actualizacion = NOW()
    WHERE id = :id";

    $stmt = $pdo->prepare($sql);


    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':direccion', $direccion);
    $stmt->bindParam(':provincia', $provincia);
    $stmt->bindParam(':canton', $canton);
    $stmt->bindParam(':distrito', $distrito);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':correo', $correo);
    $stmt->bindParam(':horario_atencion', $horario_atencion);
    $stmt->bindParam(':responsable', $responsable);
    $stmt->bindParam(':activo', $activo);
    $stmt->bindParam(':coordenadas_gps', $coordenadas_gps);
    $stmt->bindParam(':observaciones', $observaciones);
    $stmt->bindParam(':id', $id); 

    $stmt->execute();

}



public function deleteOffice($id){

    $sql = "UPDATE oficinas SET activo = 0, fecha_actualizacion = NOW() WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

}


 


} 