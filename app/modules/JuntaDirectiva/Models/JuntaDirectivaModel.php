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
        a.id AS afiliado_id,
        jd.id,
        jd.cargo, 
        jd.fecha_inicio, 
        jd.fecha_fin, 
        jd.estado, 
        jd.documentos 
        FROM {$this->table} jd
        INNER JOIN afiliados a ON jd.afiliado_id = a.id
        WHERE jd.estado IN ('Vigente','Suspendido')";
        

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function gethistorial()
    {
        $sql = "SELECT 
        a.nombre_completo AS nombre, 
        a.id AS afiliado_id,
        jd.id,
        jd.cargo, 
        jd.fecha_inicio, 
        jd.fecha_fin, 
        jd.estado, 
        jd.documentos 
        FROM {$this->table} jd
        INNER JOIN afiliados a ON jd.afiliado_id = a.id
        WHERE jd.estado = 'Finalizado'";
        

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

 
    
    public function createMiembroJunta($afiliado_id, $cargo, $estado,$fecha_inicio, $fecha_fin, $periodo,$responsabilidades, $documentos,$observaciones,$fecha_actualizacion){     
    $sql = "INSERT INTO {$this->table}(
            afiliado_id,
            cargo,
            fecha_inicio,
            fecha_fin,
            periodo,
            estado,
            responsabilidades,
            documentos,
            observaciones,
            fecha_actualizacion 

            )VALUES(
            :afiliado_id, :cargo, :fecha_inicio, :fecha_fin,:periodo,:estado,:responsabilidades,:documentos,
            :observaciones, :fecha_actualizacion)"; 

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':afiliado_id', $afiliado_id);
            $stmt->bindParam(':cargo',$cargo);
            $stmt->bindParam(':fecha_inicio', $fecha_inicio);
            $stmt->bindParam(':fecha_fin', $fecha_fin);
            $stmt->bindParam(':periodo', $periodo);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':responsabilidades', $responsabilidades);
            $stmt->bindParam(':documentos', $documentos);
            $stmt->bindParam(':observaciones',$observaciones);
            $stmt->bindParam(':fecha_actualizacion',$fecha_actualizacion);
            return $stmt->execute();  
    }
     
     
    public function getAfiliados(){
      $sql = "SELECT id, nombre_completo, cedula FROM afiliados WHERE estado = 'activo'";
      $stmt = $this->db->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }


  public function updateMiembroJunta($id,$cargo,$fecha_inicio,$fecha_fin,$periodo,$estado,$responsabilidades,$documentos,$observaciones ){
  $sql="UPDATE {$this->table}
    set cargo = :cargo,
    fecha_inicio = :fecha_inicio,
    fecha_fin = :fecha_fin,
    periodo = :periodo,
    estado = :estado,
    responsabilidades = :responsabilidades,
    documentos = :documentos,
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
    $stmt->bindParam(':documentos',$documentos);
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


   public function getMiembroById($id){
   $sql = "SELECT jd.*, a.nombre_completo AS nombre
           FROM {$this->table} jd
           INNER JOIN afiliados a ON jd.afiliado_id = a.id
           WHERE jd.id = :id";

    $stmt = $this->db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}


 
}









