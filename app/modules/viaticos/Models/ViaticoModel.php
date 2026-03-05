<?php
namespace App\Modules\Viaticos\Models;

use App\Core\ModelBase;
use PDO;
use PDOException;

class ViaticoModel extends ModelBase {
    
    protected $table = 'viaticos';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Genera un consecutivo automático (Ej: VIAT-2026-0001)
     */
    public function generarConsecutivo() {
        $anio = date('Y');
        $sql = "SELECT consecutivo FROM {$this->table} WHERE consecutivo LIKE :prefijo ORDER BY id DESC LIMIT 1";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':prefijo' => "VIAT-$anio-%"]);
            $ultimo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($ultimo && !empty($ultimo['consecutivo'])) {
                $partes = explode('-', $ultimo['consecutivo']);
                $siguienteNumero = intval($partes[2]) + 1;
            } else {
                $siguienteNumero = 1;
            }

            return "VIAT-$anio-" . str_pad($siguienteNumero, 4, '0', STR_PAD_LEFT);

        } catch (PDOException $e) {
            error_log("Error generando consecutivo de viáticos: " . $e->getMessage());
            return "VIAT-$anio-" . rand(1000, 9999);
        }
    }

    /**
     * Registra una nueva solicitud de viáticos en estado Borrador
     */
    public function crearSolicitud($datos) {
        $consecutivo = $this->generarConsecutivo();

        $sql = "INSERT INTO {$this->table} (
                    consecutivo, estado, aplica_transporte, tipo_vehiculo, kilometraje, 
                    tarifa_km, monto_transporte, enlace_maps, aplica_desayuno, 
                    aplica_almuerzo, aplica_cena, monto_alimentacion, total_pagar,
                    archivo_comprobante
                ) VALUES (
                    :consecutivo, 'Borrador', :aplica_transporte, :tipo_vehiculo, :kilometraje,
                    :tarifa_km, :monto_transporte, :enlace_maps, :aplica_desayuno,
                    :aplica_almuerzo, :aplica_cena, :monto_alimentacion, :total_pagar,
                    :archivo_comprobante
                )";

        try {
            $stmt = $this->db->prepare($sql);
            $exito = $stmt->execute([
                ':consecutivo' => $consecutivo,
                ':aplica_transporte' => $datos['aplica_transporte'] ?? 0,
                ':tipo_vehiculo' => empty($datos['tipo_vehiculo']) ? null : $datos['tipo_vehiculo'],
                ':kilometraje' => $datos['kilometraje'] ?? 0.00,
                ':tarifa_km' => $datos['tarifa_km'] ?? 0.00,
                ':monto_transporte' => $datos['monto_transporte'] ?? 0.00,
                ':enlace_maps' => empty($datos['enlace_maps']) ? null : $datos['enlace_maps'],
                ':aplica_desayuno' => $datos['aplica_desayuno'] ?? 0,
                ':aplica_almuerzo' => $datos['aplica_almuerzo'] ?? 0,
                ':aplica_cena' => $datos['aplica_cena'] ?? 0,
                ':monto_alimentacion' => $datos['monto_alimentacion'] ?? 0.00,
                ':total_pagar' => $datos['total_pagar'] ?? 0.00,
                ':archivo_comprobante' => $datos['archivo_comprobante'] ?? null
            ]);

            return $exito ? $this->db->lastInsertId() : false;

        } catch (PDOException $e) {
            error_log("Error guardando viático: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los viáticos registrados
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        try {
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo viáticos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los detalles de un viático específico por su ID
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error obteniendo viático por ID: " . $e->getMessage());
            return false;
        }
    }
}