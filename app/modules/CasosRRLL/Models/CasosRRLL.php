<?php

namespace App\Modules\CasosRRLL\Models;

use App\Core\ModelBase;
use PDO;

class CasosRRLL extends ModelBase
{
    protected $table = 'casos_relaciones_laborales';

    /**
     * Verificar si existe un expediente
     */
    public function existeExpediente($numero_expediente, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE numero_expediente = :expediente";
        $params = ['expediente' => $numero_expediente];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener todos los casos con filtros
     */
    public function getAll($filtros = [])
    {
        $sql = "SELECT c.*, 
                       a.nombre_completo as afiliado_nombre,
                       cat.nombre as categoria_nombre,
                       u_creador.nombre_completo as creado_por_nombre,
                       u_responsable.nombre_completo as responsable_nombre
                FROM {$this->table} c
                LEFT JOIN afiliados a ON c.afiliado_id = a.id
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios u_creador ON c.creado_por = u_creador.id
                LEFT JOIN usuarios u_responsable ON c.responsable_actual = u_responsable.id
                WHERE 1=1";

        $params = [];

        // Búsqueda general
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (c.numero_expediente LIKE :busqueda OR c.titulo LIKE :busqueda2 OR a.nombre_completo LIKE :busqueda3)";
            $termino = "%" . $filtros['busqueda'] . "%";
            $params['busqueda'] = $termino;
            $params['busqueda2'] = $termino;
            $params['busqueda3'] = $termino;
        }

        // Filtro por estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND c.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        // Filtro por categoría
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND c.categoria_id = :categoria_id";
            $params['categoria_id'] = $filtros['categoria_id'];
        }

        // Filtro por prioridad
        if (!empty($filtros['prioridad'])) {
            $sql .= " AND c.prioridad = :prioridad";
            $params['prioridad'] = $filtros['prioridad'];
        }

        // Filtro por afiliado
        if (!empty($filtros['afiliado_id'])) {
            $sql .= " AND c.afiliado_id = :afiliado_id";
            $params['afiliado_id'] = $filtros['afiliado_id'];
        }

        // Ordenamiento
        $sql .= " ORDER BY c.fecha_apertura DESC, c.prioridad DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener un caso por ID
     */
    public function getById($id)
    {
        $sql = "SELECT c.*, 
                       a.nombre_completo as afiliado_nombre,
                       a.cedula as afiliado_cedula,
                       cat.nombre as categoria_nombre,
                       u_creador.nombre_completo as creado_por_nombre,
                       u_responsable.nombre_completo as responsable_nombre
                FROM {$this->table} c
                LEFT JOIN afiliados a ON c.afiliado_id = a.id
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios u_creador ON c.creado_por = u_creador.id
                LEFT JOIN usuarios u_responsable ON c.responsable_actual = u_responsable.id
                WHERE c.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nuevo caso
     */
    public function create($datos)
    {
        $sql = "INSERT INTO {$this->table} 
                (numero_expediente, afiliado_id, categoria_id, titulo, descripcion, 
                 hechos, empresa_involucrada, departamento_afectado, estado, prioridad, 
                 fecha_incidente, fecha_apertura, creado_por, responsable_actual, observaciones)
                VALUES 
                (:numero_expediente, :afiliado_id, :categoria_id, :titulo, :descripcion,
                 :hechos, :empresa_involucrada, :departamento_afectado, :estado, :prioridad,
                 :fecha_incidente, :fecha_apertura, :creado_por, :responsable_actual, :observaciones)";

        $params = [
            'numero_expediente' => $datos['numero_expediente'] ?? null,
            'afiliado_id' => $datos['afiliado_id'] ?? null,
            'categoria_id' => $datos['categoria_id'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'hechos' => $datos['hechos'] ?? null,
            'empresa_involucrada' => $datos['empresa_involucrada'] ?? 'Banco Nacional',
            'departamento_afectado' => $datos['departamento_afectado'] ?? null,
            'estado' => $datos['estado'] ?? 'activo',
            'prioridad' => $datos['prioridad'] ?? 'media',
            'fecha_incidente' => $datos['fecha_incidente'] ?? null,
            'fecha_apertura' => $datos['fecha_apertura'] ?? date('Y-m-d'),
            'creado_por' => $datos['creado_por'] ?? ($_SESSION['usuario_id'] ?? 1),
            'responsable_actual' => $datos['responsable_actual'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Actualizar caso
     */
    public function update($id, $datos)
    {
        $sql = "UPDATE {$this->table} SET 
                numero_expediente = :numero_expediente,
                afiliado_id = :afiliado_id,
                categoria_id = :categoria_id,
                titulo = :titulo,
                descripcion = :descripcion,
                hechos = :hechos,
                empresa_involucrada = :empresa_involucrada,
                departamento_afectado = :departamento_afectado,
                estado = :estado,
                prioridad = :prioridad,
                fecha_incidente = :fecha_incidente,
                responsable_actual = :responsable_actual,
                resultado_final = :resultado_final,
                observaciones = :observaciones
                WHERE id = :id";

        $params = [
            'id' => $id,
            'numero_expediente' => $datos['numero_expediente'] ?? null,
            'afiliado_id' => $datos['afiliado_id'] ?? null,
            'categoria_id' => $datos['categoria_id'],
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'hechos' => $datos['hechos'] ?? null,
            'empresa_involucrada' => $datos['empresa_involucrada'] ?? 'Banco Nacional',
            'departamento_afectado' => $datos['departamento_afectado'] ?? null,
            'estado' => $datos['estado'] ?? 'activo',
            'prioridad' => $datos['prioridad'] ?? 'media',
            'fecha_incidente' => $datos['fecha_incidente'] ?? null,
            'responsable_actual' => $datos['responsable_actual'] ?? null,
            'resultado_final' => $datos['resultado_final'] ?? null,
            'observaciones' => $datos['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Cambiar estado del caso
     */
    public function cambiarEstado($id, $nuevoEstado)
    {
        $estados_validos = ['activo', 'en_progreso', 'cerrado', 'archivado', 'suspendido'];

        if (!in_array($nuevoEstado, $estados_validos)) {
            return false;
        }

        $sql = "UPDATE {$this->table} SET estado = :estado, fecha_actualizacion = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['estado' => $nuevoEstado, 'id' => $id]);
    }

    /**
     * Cambiar responsable del caso
     */
    public function cambiarResponsable($id, $responsableId)
    {
        $sql = "UPDATE {$this->table} 
                SET responsable_actual = :responsable_id, 
                    fecha_actualizacion = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['responsable_id' => ($responsableId ?: null), 'id' => $id]);
    }

    /**
     * Archivar caso
     */
    public function archivar($id, $resultado = null)
    {
        $sql = "UPDATE {$this->table} 
                SET estado = 'archivado', 
                    fecha_cierre = NOW(),
                    resultado_final = COALESCE(:resultado, resultado_final),
                    fecha_actualizacion = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['resultado' => $resultado, 'id' => $id]);
    }

    /**
     * Eliminar caso
     */
    public function delete($id)
    {
        // Primero eliminamos las etapas asociadas
        $sqlEtapas = "DELETE FROM etapas_casos WHERE caso_id = :id";
        $stmtEtapas = $this->db->prepare($sqlEtapas);
        $stmtEtapas->execute(['id' => $id]);

        // Luego eliminamos el caso
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Obtener categorías tipo caso_rrll
     */
    public function getCategorias()
    {
        $sql = "SELECT id, nombre FROM categorias 
                WHERE estado = 'activo' AND tipo IN ('caso_rrll', 'general') 
                ORDER BY nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los afiliados activos
     */
    public function getAfiliados()
    {
        $sql = "SELECT id, nombre_completo, cedula 
                FROM afiliados 
                WHERE estado = 'activo' 
                ORDER BY nombre_completo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener todos los usuarios responsables
     */
    public function getUsuarios()
    {
        $sql = "SELECT id, nombre_completo, username 
                FROM usuarios 
                WHERE estado = 'activo' 
                ORDER BY nombre_completo ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener etapas de un caso
     */
    public function getEtapasCaso($casoId)
    {
        $sql = "SELECT * FROM etapas_casos 
                WHERE caso_id = :caso_id 
                ORDER BY orden ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar etapas finalizadas de un caso
     */
    public function contarEtapasFinalizadas($casoId)
    {
        $sql = "SELECT COUNT(*) FROM etapas_casos 
                WHERE caso_id = :caso_id AND estado = 'finalizado'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchColumn();
    }

    /**
     * Contar total de etapas de un caso
     */
    public function contarTotalEtapas($casoId)
    {
        $sql = "SELECT COUNT(*) FROM etapas_casos WHERE caso_id = :caso_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['caso_id' => $casoId]);
        return $stmt->fetchColumn();
    }

    /**
     * Genera un número de expediente único
     */
    public function generarNumeroExpediente()
    {
        $prefijo = 'EXP-' . date('Y') . '-';
        $sql = "SELECT MAX(CAST(SUBSTRING(numero_expediente, -5) AS UNSIGNED)) as max_num 
                FROM {$this->table} 
                WHERE numero_expediente LIKE :prefijo";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['prefijo' => $prefijo . '%']);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        $numero = ($resultado['max_num'] ?? 0) + 1;
        return $prefijo . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}
