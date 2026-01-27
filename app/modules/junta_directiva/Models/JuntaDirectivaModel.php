<?php

namespace App\Modules\junta_directiva\Models;
use App\Core\ModelBase;

class JuntaDirectivaModel extends ModelBase
{
    protected $table = "junta_directiva";


    /*
        public function getById($id) {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }

    */


    /*
        public function getJuntaDirectiva(){
            $sql = "SELECT * FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);


        }

    */
    public function getJuntaDirectiva()
    {
        $sql = "SELECT 
        a.nombre, 
        a.id AS afiliado_id,
        jd.cargo, 
        jd.fecha_inicio, 
        jd.fecha_fin, 
        jd.estado, 
        jd.documentos 
        FROM {$this->table} jd
        INNER JOIN afiliados a ON jd.afiliado_id = a.id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }
}









