<?php
namespace App\Modules\Afiliados\Models;

use App\Core\ModelBase;

use PDO;

class Afiliados extends ModelBase
{

    protected $table = 'afiliados';

    public function getByCedula($cedula)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE cedula = :cedula LIMIT 1");
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByCorreo($correo)
    {
        $correo = strtolower(trim((string) $correo));
        if ($correo === '') {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE LOWER(correo) = :correo LIMIT 1");
        $stmt->execute(['correo' => $correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existeCedula($cedula, $idExcluir = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE cedula = :cedula";
        $params = ['cedula' => $cedula];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function existeCorreo($correo, $idExcluir = null)
    {
        $correo = strtolower(trim((string) $correo));
        if ($correo === '') {
            return false;
        }

        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE LOWER(correo) = :correo";
        $params = ['correo' => $correo];

        if ($idExcluir) {
            $sql .= " AND id != :id";
            $params['id'] = $idExcluir;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Obtener listado de Categorías (Tabla 'categorias')
     * Tipo 'afiliado' o 'general'
     */
    public function getCategorias()
    {
        $sql = "SELECT id, nombre FROM categorias WHERE estado = 'activo' AND tipo IN ('afiliado', 'general') ORDER BY nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener listado de Oficinas (Tabla 'oficinas')
     */
    public function getOficinas()
    {
        $sql = "SELECT id, nombre, codigo FROM oficinas WHERE activo = 1 ORDER BY nombre ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($filtros = [], $start = 0, $limit = 10)
    {
        // Iniciamos la consulta base. 
        // JOINs para mostrar nombres en lugar de IDs si fuera necesario, 
        // pero para el listado básico a veces basta con lo que hay o se cargan.
        // Aquí haremos JOIN para mostrar nombre de categoría y oficina en el listado.
        $sql = "SELECT a.*, c.nombre as categoria_nombre, o.nombre as oficina_nombre 
                FROM {$this->table} a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                LEFT JOIN oficinas o ON a.oficina_id = o.id
                WHERE 1=1";

        $params = [];

        // Filtro 1: Búsqueda General
        if (!empty($filtros['busqueda'])) {
            $sql .= " AND (a.nombre_completo LIKE :b1 OR a.cedula LIKE :b2)";
            $termino = "%" . $filtros['busqueda'] . "%";
            $params['b1'] = $termino;
            $params['b2'] = $termino;
        }

        // Filtro 2: Estado
        if (!empty($filtros['estado'])) {
            $sql .= " AND a.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        // Filtro 3: Oficina (Nuevo)
        if (!empty($filtros['oficina_id'])) {
            $sql .= " AND a.oficina_id = :oficina_id";
            $params['oficina_id'] = $filtros['oficina_id'];
        }

        // Ordenamiento por defecto
        $sql .= " ORDER BY a.fecha_creacion DESC
          LIMIT {$start}, {$limit}";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //paginacion
    public function countAll($filtros = [])
{
    $sql = "SELECT COUNT(*) 
            FROM {$this->table} a
            WHERE 1=1";

    $params = [];

    // Filtro 1: Búsqueda
    if (!empty($filtros['busqueda'])) {
        $sql .= " AND (a.nombre_completo LIKE :b1 OR a.cedula LIKE :b2)";
        $termino = "%" . $filtros['busqueda'] . "%";
        $params['b1'] = $termino;
        $params['b2'] = $termino;
    }

    // Filtro 2: Estado
    if (!empty($filtros['estado'])) {
        $sql .= " AND a.estado = :estado";
        $params['estado'] = $filtros['estado'];
    }

    // Filtro 3: Oficina
    if (!empty($filtros['oficina_id'])) {
        $sql .= " AND a.oficina_id = :oficina_id";
        $params['oficina_id'] = $filtros['oficina_id'];
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}


    public function getById($id)
    {
        // Obtenemos el registro. Para edicion, necesitamos IDs, no nombres.
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $sql = "INSERT INTO {$this->table} (
                    nombre, apellido1, apellido2, cedula, 
                    genero, fecha_nacimiento, correo, telefono, 
                    telefono_secundario, direccion, categoria_id, oficina_id,
                    puesto_actual, datos_contacto_emergencia, observaciones,
                    estado, fecha_creacion
                ) VALUES (
                    :nombre, :apellido1, :apellido2, :cedula, 
                    :genero, :fecha_nacimiento, :correo, :telefono,
                    :telefono_secundario, :direccion, :categoria_id, :oficina_id,
                    :puesto_actual, :datos_contacto_emergencia, :observaciones,
                    'activo', NOW()
                )";

        $params = [
            'nombre' => trim((string) ($data['nombre'] ?? '')),
            'apellido1' => trim((string) ($data['apellido1'] ?? '')),
            'apellido2' => trim((string) ($data['apellido2'] ?? '')),
            'cedula' => trim((string) ($data['cedula'] ?? '')),
            'genero' => $this->normalizeGenero($data['genero'] ?? null),
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            'correo' => trim((string) ($data['correo'] ?? '')) ?: null,
            'telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
            'telefono_secundario' => $data['telefono_secundario'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'categoria_id' => !empty($data['categoria_id']) ? $data['categoria_id'] : null,
            'oficina_id' => !empty($data['oficina_id']) ? $data['oficina_id'] : null,
            'puesto_actual' => $data['puesto_actual'] ?? null,
            'datos_contacto_emergencia' => $data['datos_contacto_emergencia'] ?? '{}', // JSON string
            'observaciones' => $data['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($params)) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET 
                nombre = :nombre,
                apellido1 = :apellido1,
                apellido2 = :apellido2,
                cedula = :cedula,
                genero = :genero,
                fecha_nacimiento = :fecha_nacimiento,
                correo = :correo,
                telefono = :telefono,
                telefono_secundario = :telefono_secundario,
                direccion = :direccion,
                categoria_id = :categoria_id,
                oficina_id = :oficina_id,
                puesto_actual = :puesto_actual,
                datos_contacto_emergencia = :datos_contacto_emergencia,
                observaciones = :observaciones,
                fecha_actualizacion = NOW()
                WHERE id = :id";

        $params = [
            'id' => $id,
            'nombre' => trim((string) ($data['nombre'] ?? '')),
            'apellido1' => trim((string) ($data['apellido1'] ?? '')),
            'apellido2' => trim((string) ($data['apellido2'] ?? '')),
            'cedula' => trim((string) ($data['cedula'] ?? '')),
            'genero' => $this->normalizeGenero($data['genero'] ?? null),
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?: null,
            'correo' => trim((string) ($data['correo'] ?? '')) ?: null,
            'telefono' => trim((string) ($data['telefono'] ?? '')) ?: null,
            'telefono_secundario' => $data['telefono_secundario'] ?? null,
            'direccion' => $data['direccion'] ?? null,
            'categoria_id' => !empty($data['categoria_id']) ? $data['categoria_id'] : null,
            'oficina_id' => !empty($data['oficina_id']) ? $data['oficina_id'] : null,
            'puesto_actual' => $data['puesto_actual'] ?? null,
            'datos_contacto_emergencia' => $data['datos_contacto_emergencia'] ?? '{}', // JSON string
            'observaciones' => $data['observaciones'] ?? null
        ];

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function toggleStatus($id)
    {
        $current = $this->getById($id);
        if (!$current)
            return false;

        $nuevoEstado = ($current['estado'] === 'activo') ? 'inactivo' : 'activo';

        $stmt = $this->db->prepare("UPDATE {$this->table} SET estado = :estado WHERE id = :id");
        $stmt->execute(['estado' => $nuevoEstado, 'id' => $id]);

        return $nuevoEstado; // Retornamos el nuevo estado para loguearlo
    }
    public function registrarBaja($id, $data)
{
    $sql = "UPDATE {$this->table} SET
                estado = 'inactivo',
                fecha_baja = :fecha_baja,
                motivo_baja = :motivo_baja,
                tipo_baja = :tipo_baja
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'fecha_baja' => $data['fecha_baja'],
        'motivo_baja' => $data['motivo_baja'],
        'tipo_baja' => $data['tipo_baja'],
        'id' => $id
    ]);
}

    private function normalizeGenero($value)
    {
        $genero = strtolower(trim((string) $value));
        $permitidos = ['masculino', 'femenino', 'otro', 'prefiero_no_decir'];

        if (!in_array($genero, $permitidos, true)) {
            return 'prefiero_no_decir';
        }

        return $genero;
    }

public function searchByCedula($cedula)
{
    $sql = "SELECT 
                id, 
                CONCAT(nombre, ' ', apellido1, ' ', apellido2) as nombre_completo, 
                cedula,
                telefono
            FROM afiliados
            WHERE estado = 'activo'
            AND cedula LIKE :cedula
            LIMIT 10";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':cedula' => '%' . $cedula . '%'
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
