<?php

namespace App\Modules\JuntaDirectiva\Models;
use App\Core\ModelBase;

class JuntaDirectivaModel extends ModelBase
{

 protected $table = "junta_directiva";
     
 public function getJuntaDirectiva()
{
    $sql = "SELECT 
        a.nombre_completo AS nombre,
        jd.id,
        jd.cargo,
        jd.fecha_inicio,
        jd.fecha_fin,
        jd.estado,
        COUNT(d.id) AS total_documentos
    FROM junta_directiva jd
    INNER JOIN afiliados a ON jd.afiliado_id = a.id
    LEFT JOIN documentos_junta d ON d.junta_id = jd.id
    WHERE jd.estado IN ('Vigente','Suspendido')
    GROUP BY 
        jd.id,
        a.nombre_completo,
        jd.cargo,
        jd.fecha_inicio,
        jd.fecha_fin,
        jd.estado";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    public function gethistorial()
    {
        $sql = "SELECT 
        a.nombre_completo AS nombre,
        jd.id,
        jd.cargo,
        jd.fecha_inicio,
        jd.fecha_fin,
        jd.estado,
        COUNT(d.id) AS total_documentos
    FROM junta_directiva jd
    INNER JOIN afiliados a ON jd.afiliado_id = a.id
    LEFT JOIN documentos_junta d ON d.junta_id = jd.id
    WHERE jd.estado IN ('Finalizado')
    GROUP BY 
        jd.id,
        a.nombre_completo,
        jd.cargo,
        jd.fecha_inicio,
        jd.fecha_fin,
        jd.estado";
        

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

 
    
    public function createMiembroJunta($afiliado_id, $cargo, $estado,$fecha_inicio, $fecha_fin, $periodo,$responsabilidades,$observaciones,$fecha_actualizacion){     
    $sql = "INSERT INTO {$this->table}(
            afiliado_id,
            cargo,
            fecha_inicio,
            fecha_fin,
            periodo,
            estado,
            responsabilidades,
            observaciones,
            fecha_actualizacion 

            )VALUES(
            :afiliado_id, :cargo, :fecha_inicio, :fecha_fin,:periodo,:estado,:responsabilidades,
            :observaciones, :fecha_actualizacion)"; 

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':afiliado_id', $afiliado_id);
            $stmt->bindParam(':cargo',$cargo);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->bindParam(':periodo', $periodo);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':responsabilidades', $responsabilidades);
            $stmt->bindParam(':observaciones',$observaciones);
            $stmt->bindParam(':fecha_actualizacion',$fecha_actualizacion);

            $stmt->execute();
            return $this->db->lastInsertId(); 
    }
     
     
    public function getAfiliados(){
      $sql = "SELECT id, nombre_completo, cedula FROM afiliados WHERE estado = 'activo'";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }


  public function updateMiembroJunta($id,$cargo,$fecha_inicio,$fecha_fin,$periodo,$estado,$responsabilidades,$observaciones ){
  $sql="UPDATE {$this->table}
    set cargo = :cargo,
    fecha_inicio = :fecha_inicio,
    fecha_fin = :fecha_fin,
    periodo = :periodo,
    estado = :estado,
    responsabilidades = :responsabilidades,
    observaciones = :observaciones 
    WHERE id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id',$id);
    $stmt->bindParam(':cargo',$cargo);
    $stmt->bindParam(':fecha_inicio',$fecha_inicio);
    $stmt->bindParam(':fecha_fin',$fecha_fin);
    $stmt->bindParam(':periodo',$periodo);
    $stmt->bindParam(':estado',$estado);
    $stmt->bindParam(':responsabilidades',$responsabilidades);
    $stmt->bindParam(':observaciones',$observaciones);
    return $stmt->execute();
   } 


public function updateEstadoFinalizar($id, $estado)
{
    $sql="UPDATE {$this->table}
    set estado = :estado
    WHERE id = :id AND estado = 'Vigente'";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id',$id);
    $stmt->bindParam(':estado',$estado);
    return $stmt->execute();

}

   
public function updateEstadoActivar($id, $estado)
{
    $sql="UPDATE {$this->table}
    set estado = :estado
    WHERE id = :id AND estado = 'finalizado'";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id',$id);
    $stmt->bindParam(':estado',$estado);
    return $stmt->execute();

}


public function getMiembroById($id)
{
   $sql = "SELECT jd.*, a.nombre_completo AS nombre
           FROM {$this->table} jd
           INNER JOIN afiliados a ON jd.afiliado_id = a.id
           WHERE jd.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}






public function insertDocumento($juntaId, $archivo, $original)
{
    $sql = "INSERT INTO documentos_junta
            (junta_id, nombre_archivo, nombre_original)
            VALUES(?,?,?)";
    
    return $this->db->prepare($sql)->execute([$juntaId, $archivo, $original]);
}


public function getDocumentos($juntaId)
{
    $sql = "SELECT * FROM documentos_junta WHERE junta_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$juntaId]);
    return $stmt->fetchAll();
}

public function getDocumentoById($id)
{
    $sql = "SELECT * FROM documentos_junta WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

public function deleteDocumento($id){
    $sql = "DELETE FROM documentos_junta WHERE id= ?";
    return $this->db->prepare($sql)->execute([$id]);
}



 
}









