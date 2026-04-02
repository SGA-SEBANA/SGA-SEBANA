<?php

namespace App\Modules\JuntaDirectiva\Models;
use App\Core\ModelBase;

class JuntaDirectivaModel extends ModelBase
{

    protected $table = "junta_directiva";
    private $lastError = '';

    public function getLastError(): string
    {
        return $this->lastError;
    }

    public function getJuntaDirectiva()
    {
        // Auto-update status for expired memberships
        $this->checkAndCloseExpiredMemberships();

        $sql = "SELECT 
        a.nombre_completo AS nombre,
        jd.id,
        jd.cargo,
        jd.fecha_inicio,
        jd.fecha_fin,
        jd.estado,
        jd.documentos

        FROM junta_directiva jd
        INNER JOIN afiliados a ON jd.afiliado_id = a.id
        WHERE jd.estado IN ('vigente','suspendido','Vigente','Suspendido')
        ORDER BY jd.fecha_inicio DESC";       


        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $docs = json_decode($row['documentos'] ?? '[]', true);
            $row['total_documentos'] = is_array($docs) ? count($docs) : 0;
            unset($row['documentos']);

        }

        return $results;

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
        jd.documentos

        FROM junta_directiva jd
        INNER JOIN afiliados a ON jd.afiliado_id = a.id
        WHERE jd.estado IN ('finalizado','Finalizado')
        ORDER BY jd.fecha_inicio DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $docs = json_decode($row['documentos'] ?? '[]', true);
            $row['total_documentos'] = is_array($docs) ? count($docs) : 0;
            unset($row['documentos']);
        }
        
        return $results;
    }


   public function getCargosActivos()
   {
    
       $sql = "SELECT DISTINCT cargo FROM {$this->table} WHERE LOWER(estado) IN ('vigente','suspendido')";
       $stmt = $this->db->prepare($sql);
       $stmt->execute();
       return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'cargo');

   }


   public function cargoExists($cargo)
   {

       $sql = "SELECT COUNT(*) as c FROM {$this->table} WHERE LOWER(cargo) = LOWER(:cargo) AND LOWER(estado) IN ('vigente','suspendido')";
       $stmt = $this->db->prepare($sql);
       $stmt->bindParam(':cargo', $cargo);
       $stmt->execute();
       $result = $stmt->fetch(\PDO::FETCH_ASSOC);
       return !empty($result) && (int)$result['c'] > 0;

   }



    public function createMiembroJunta($afiliado_id, $cargo, $estado, $fecha_inicio, $fecha_fin, $periodo, $responsabilidades, $observaciones, $fecha_actualizacion)
    {
        $this->lastError = '';

        $sql = "INSERT INTO {$this->table}(
                    afiliado_id,
                    cargo,
                    fecha_inicio,
                    fecha_fin,
                    periodo,
                    estado,
                    responsabilidades,
                    observaciones,
                    fecha_actualizacion,
                    documentos
                ) VALUES (
                    :afiliado_id, :cargo, :fecha_inicio, :fecha_fin, :periodo, :estado, :responsabilidades,
                    :observaciones, :fecha_actualizacion, :documentos
                )";

        $allowedStates = ['vigente', 'suspendido', 'finalizado'];
        $estado = strtolower(trim((string) $estado));
        if (!in_array($estado, $allowedStates, true)) {
            $estado = 'vigente';
        }

        $fecha_fin = trim((string) $fecha_fin);
        $periodo = trim((string) $periodo);
        $responsabilidades = trim((string) $responsabilidades);
        $observaciones = trim((string) $observaciones);

        $params = [
            ':afiliado_id' => (int) $afiliado_id,
            ':cargo' => trim((string) $cargo),
            ':fecha_inicio' => trim((string) $fecha_inicio),
            ':fecha_fin' => $fecha_fin !== '' ? $fecha_fin : null,
            ':periodo' => $periodo !== '' ? $periodo : null,
            ':estado' => $estado,
            ':responsabilidades' => $responsabilidades !== '' ? $responsabilidades : null,
            ':observaciones' => $observaciones !== '' ? $observaciones : null,
            ':fecha_actualizacion' => $fecha_actualizacion,
            ':documentos' => json_encode([])
        ];

        try {
            $stmt = $this->db->prepare($sql);
            $ok = $stmt->execute($params);
            return $ok ? $this->db->lastInsertId() : false;
        } catch (\PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }


    public function getAfiliados()
    {
        $sql = "SELECT id, nombre_completo, cedula FROM afiliados WHERE estado = 'activo'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function updateMiembroJunta($id, $cargo, $fecha_inicio, $fecha_fin, $periodo, $estado, $responsabilidades, $observaciones)
    {
        $this->lastError = '';

        $sql = "UPDATE {$this->table}
                SET cargo = :cargo,
                    fecha_inicio = :fecha_inicio,
                    fecha_fin = :fecha_fin,
                    periodo = :periodo,
                    estado = :estado,
                    responsabilidades = :responsabilidades,
                    observaciones = :observaciones
                WHERE id = :id";

        $allowedStates = ['vigente', 'suspendido', 'finalizado'];
        $estado = strtolower(trim((string) $estado));
        if (!in_array($estado, $allowedStates, true)) {
            $estado = 'vigente';
        }

        $fecha_fin = trim((string) $fecha_fin);
        $periodo = trim((string) $periodo);
        $responsabilidades = trim((string) $responsabilidades);
        $observaciones = trim((string) $observaciones);

        $params = [
            ':id' => (int) $id,
            ':cargo' => trim((string) $cargo),
            ':fecha_inicio' => trim((string) $fecha_inicio),
            ':fecha_fin' => $fecha_fin !== '' ? $fecha_fin : null,
            ':periodo' => $periodo !== '' ? $periodo : null,
            ':estado' => $estado,
            ':responsabilidades' => $responsabilidades !== '' ? $responsabilidades : null,
            ':observaciones' => $observaciones !== '' ? $observaciones : null,
        ];

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }


    public function updateEstadoFinalizar($id, $estado)
    {
            
        $sql = "UPDATE {$this->table}
        set estado = :estado
        WHERE id = :id AND estado = 'vigente'";

        $estado = strtolower($estado);

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':estado', $estado);
        return $stmt->execute();

    }


    public function updateEstadoActivar($id, $estado)
    {
        
        $sql = "UPDATE {$this->table}
        set estado = :estado
        WHERE id = :id AND estado = 'finalizado'";

        $estado = strtolower($estado);

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':estado', $estado);
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
        $sql = "SELECT documentos FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$juntaId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $docs = [];
        if ($result && !empty($result['documentos'])) {
            $docs = json_decode($result['documentos'], true);
            if (!is_array($docs)) {
                $docs = [];
            }
        }

        $newDoc = [
            'id' => uniqid(),
            'nombre_archivo' => $archivo,
            'nombre_original' => $original,
            'fecha_subida' => date('Y-m-d H:i:s')
        ];
        $docs[] = $newDoc;

        $jsonDocs = json_encode($docs);
        $updateSql = "UPDATE {$this->table} SET documentos = ? WHERE id = ?";
        return $this->db->prepare($updateSql)->execute([$jsonDocs, $juntaId]);
    }


    public function getDocumentos($juntaId)
    {
        $sql = "SELECT documentos FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$juntaId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $finalDocs = [];
        if ($result && !empty($result['documentos'])) {
            $docs = json_decode($result['documentos'], true);
            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    $doc['id'] = $juntaId . '_' . $doc['id'];
                    $finalDocs[] = $doc;
                }
            }
        }
        return $finalDocs;
    }

    public function getDocumentoById($id)
    {
        $parts = explode('_', $id);
        if (count($parts) < 2) {
            return null;
        }

        $juntaId = $parts[0];
        $docId = $parts[1];

        $sql = "SELECT documentos FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$juntaId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);


        if ($result && !empty($result['documentos'])) {
            $docs = json_decode($result['documentos'], true);
            if (is_array($docs)) {
                foreach ($docs as $doc) {
                    if ($doc['id'] === $docId) {
                        $doc['id'] = $id;
                        return $doc;
                    }
                }
            }
        }

        return null;

    }


    public function deleteDocumento($id)
    {
        $parts = explode('_', $id);
        if (count($parts) < 2) {
            return false;
        }

        $juntaId = $parts[0];
        $docId = $parts[1];

        $sql = "SELECT documentos FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$juntaId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result && !empty($result['documentos'])) {
            $docs = json_decode($result['documentos'], true);
            if (is_array($docs)) {
                $newDocs = [];
                $found = false;
                foreach ($docs as $doc) {
                    if ($doc['id'] === $docId) {
                        $found = true;
                        continue;
                    }
                    $newDocs[] = $doc;
                }

                if ($found) {
                    $jsonDocs = json_encode($newDocs);
                    $updateSql = "UPDATE {$this->table} SET documentos = ? WHERE id = ?";
                    return $this->db->prepare($updateSql)->execute([$jsonDocs, $juntaId]);
                }
            }
        }
        return false;
    }

    public function checkAndCloseExpiredMemberships()
    {
        // Update status to 'finalizado' for records where end date has passed and status is not already finalized
        $sql = "UPDATE {$this->table} 
                SET estado = 'finalizado' 
                WHERE fecha_fin < CURDATE() 
                AND estado NOT IN ('finalizado', 'Finalizado')";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
    }


}
