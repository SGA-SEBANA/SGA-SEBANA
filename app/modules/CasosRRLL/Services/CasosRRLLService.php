<?php

namespace App\Modules\CasosRRLL\Services;

use App\Core\Database;
use App\Modules\CasosRRLL\Models\CasosRRLL;
use App\Modules\CasosRRLL\Models\Etapas;
use App\Modules\Usuarios\Models\Bitacora;
use App\Modules\Visitas\Models\Notification;

class CasosRRLLService
{
    private const TRANSICIONES_CASO = [
        'activo' => ['en_progreso', 'suspendido', 'cerrado', 'archivado'],
        'en_progreso' => ['suspendido', 'cerrado', 'archivado'],
        'suspendido' => ['en_progreso', 'cerrado', 'archivado'],
        'cerrado' => ['archivado'],
        'archivado' => []
    ];

    private CasosRRLL $casos;
    private Etapas $etapas;
    private Bitacora $bitacora;
    private Notification $notificaciones;
    private \PDO $db;

    public function __construct()
    {
        $this->casos = new CasosRRLL();
        $this->etapas = new Etapas();
        $this->bitacora = new Bitacora();
        $this->notificaciones = new Notification();
        $this->db = Database::getInstance()->getConnection();
    }

    public function crearCaso(array $datos): array
    {
        return $this->inTransaction(function () use ($datos) {
            $datos['estado'] = 'activo';
            $id = $this->casos->create($datos);

            if ($id === false) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Creacion de caso: {$datos['numero_expediente']}",
                'datos_nuevos' => [
                    'numero_expediente' => $datos['numero_expediente'] ?? null,
                    'titulo' => $datos['titulo'] ?? null,
                    'categoria_id' => $datos['categoria_id'] ?? null,
                    'estado' => 'activo'
                ]
            ]);

            $this->notificaciones->createNotification(
                1,
                'sistema',
                'casos',
                'Nuevo Caso RRLL',
                "Se creo el caso con expediente {$datos['numero_expediente']}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );

            return ['ok' => true, 'id' => $id];
        });
    }

    public function actualizarCasoFijo(int $id, array $datos): array
    {
        return $this->inTransaction(function () use ($id, $datos) {
            $anterior = $this->casos->getById($id);
            if (!$anterior) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            $payload = [
                'numero_expediente' => $datos['numero_expediente'] ?? $anterior['numero_expediente'],
                'afiliado_id' => $datos['afiliado_id'] ?? $anterior['afiliado_id'],
                'categoria_id' => $datos['categoria_id'] ?? $anterior['categoria_id'],
                'titulo' => $datos['titulo'] ?? $anterior['titulo'],
                'descripcion' => $datos['descripcion'] ?? $anterior['descripcion'],
                'hechos' => $datos['hechos'] ?? $anterior['hechos'],
                'empresa_involucrada' => $datos['empresa_involucrada'] ?? $anterior['empresa_involucrada'],
                'departamento_afectado' => $datos['departamento_afectado'] ?? $anterior['departamento_afectado'],
                'prioridad' => $datos['prioridad'] ?? $anterior['prioridad'],
                'fecha_incidente' => $datos['fecha_incidente'] ?? $anterior['fecha_incidente'],
                'responsable_actual' => $datos['responsable_actual'] ?? $anterior['responsable_actual'],
                'observaciones' => $datos['observaciones'] ?? $anterior['observaciones'],
                // Estado y resultado no se editan por formulario general
                'estado' => $anterior['estado'],
                'resultado_final' => $anterior['resultado_final']
            ];

            if (!$this->casos->update($id, $payload)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Actualizacion de caso: {$payload['numero_expediente']}",
                'datos_anteriores' => $this->sliceCaseForAudit($anterior),
                'datos_nuevos' => $this->sliceCaseForAudit(array_merge($anterior, $payload))
            ]);

            $this->notificaciones->createNotification(
                1,
                'sistema',
                'casos',
                'Caso RRLL Actualizado',
                "Se actualizo el caso ID {$id}, expediente {$payload['numero_expediente']}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );

            return ['ok' => true];
        });
    }

    public function cambiarEstadoCaso(int $id, string $nuevoEstado, ?string $resultadoFinal = null): array
    {
        return $this->inTransaction(function () use ($id, $nuevoEstado, $resultadoFinal) {
            $caso = $this->casos->getById($id);
            if (!$caso) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            $estadoActual = $this->normalizeCaseState((string) ($caso['estado'] ?? ''));
            $nuevoEstado = $this->normalizeCaseState($nuevoEstado);

            if (!array_key_exists($nuevoEstado, self::TRANSICIONES_CASO) && $nuevoEstado !== 'archivado') {
                return ['ok' => false, 'error' => 'transicion_invalida'];
            }

            $transicionesPermitidas = self::TRANSICIONES_CASO[$estadoActual] ?? self::TRANSICIONES_CASO['activo'];
            if (!in_array($nuevoEstado, $transicionesPermitidas, true)) {
                return ['ok' => false, 'error' => 'transicion_invalida'];
            }

            if ($nuevoEstado === 'cerrado') {
                $etapasActivas = $this->casos->contarEtapasActivas($id);
                if ($etapasActivas > 0) {
                    return ['ok' => false, 'error' => 'etapas_activas'];
                }
                if (trim((string) $resultadoFinal) === '') {
                    return ['ok' => false, 'error' => 'resultado_requerido'];
                }
            }

            if (!$this->casos->cambiarEstado($id, $nuevoEstado, $resultadoFinal)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'STATUS_CHANGE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Cambio de estado del caso a: {$nuevoEstado}",
                'datos_anteriores' => ['estado' => $estadoActual, 'resultado_final' => $caso['resultado_final'] ?? null],
                'datos_nuevos' => ['estado' => $nuevoEstado, 'resultado_final' => $resultadoFinal]
            ]);

            $this->notificaciones->createNotification(
                1,
                'sistema',
                'casos',
                'Estado de Caso Cambiado',
                "El caso ID {$id} ahora esta en estado: {$nuevoEstado}",
                'caso_rrll',
                $id,
                "/SGA-SEBANA/public/casos-rrll/show/{$id}"
            );

            return ['ok' => true];
        });
    }

    public function cambiarResponsable(int $id, $responsableId): array
    {
        return $this->inTransaction(function () use ($id, $responsableId) {
            $caso = $this->casos->getById($id);
            if (!$caso) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            if (!$this->casos->cambiarResponsable($id, $responsableId)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => 'Cambio de responsable del caso',
                'datos_anteriores' => ['responsable_actual' => $caso['responsable_actual'] ?? null],
                'datos_nuevos' => ['responsable_actual' => $responsableId ?: null]
            ]);

            return ['ok' => true];
        });
    }

    public function archivarCaso(int $id, ?string $resultadoFinal = null): array
    {
        return $this->cambiarEstadoCaso($id, 'archivado', $resultadoFinal);
    }

    public function eliminarCasoLogico(int $id): array
    {
        return $this->inTransaction(function () use ($id) {
            $caso = $this->casos->getById($id);
            if (!$caso) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            if (!$this->casos->delete($id)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'SOFT_DELETE',
                'modulo' => 'casos_rrll',
                'entidad' => 'caso',
                'entidad_id' => $id,
                'descripcion' => "Archivo logico del caso: {$caso['numero_expediente']}",
                'datos_anteriores' => ['estado' => $caso['estado'] ?? null],
                'datos_nuevos' => ['estado' => 'archivado']
            ]);

            return ['ok' => true];
        });
    }

    public function crearEtapa(int $casoId, array $datos): array
    {
        return $this->inTransaction(function () use ($casoId, $datos) {
            $caso = $this->casos->getById($casoId);
            if (!$caso) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            $orden = isset($datos['orden']) && (int) $datos['orden'] > 0
                ? (int) $datos['orden']
                : $this->etapas->obtenerSiguienteOrden($casoId);

            if ($this->etapas->existeOrdenEnCaso($casoId, $orden)) {
                return ['ok' => false, 'error' => 'orden_duplicado'];
            }

            $datos['caso_id'] = $casoId;
            $datos['orden'] = $orden;
            $etapaId = $this->etapas->create($datos);

            if ($etapaId === false) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Creacion de etapa: {$datos['nombre']} para caso {$casoId}",
                'datos_nuevos' => [
                    'caso_id' => $casoId,
                    'nombre' => $datos['nombre'] ?? null,
                    'orden' => $orden,
                    'estado' => $datos['estado'] ?? 'pendiente'
                ]
            ]);

            $this->notificaciones->createNotification(
                1,
                'sistema',
                'casos',
                'Nueva Etapa en Caso RRLL',
                "Se creo la etapa '{$datos['nombre']}' en el caso ID {$casoId}",
                'etapa_rrll',
                $etapaId,
                "/SGA-SEBANA/public/casos-rrll/{$casoId}/etapas"
            );

            return ['ok' => true, 'id' => $etapaId];
        });
    }

    public function actualizarEtapa(int $etapaId, array $datos): array
    {
        return $this->inTransaction(function () use ($etapaId, $datos) {
            $etapa = $this->etapas->getById($etapaId);
            if (!$etapa) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            $orden = isset($datos['orden']) && (int) $datos['orden'] > 0
                ? (int) $datos['orden']
                : (int) $etapa['orden'];

            if ($this->etapas->existeOrdenEnCaso((int) $etapa['caso_id'], $orden, $etapaId)) {
                return ['ok' => false, 'error' => 'orden_duplicado'];
            }

            $nuevoEstado = (string) ($datos['estado'] ?? $etapa['estado']);
            if (in_array($nuevoEstado, ['en_progreso', 'finalizado', 'bloqueado'], true)) {
                if ($this->etapas->existeEtapaPreviaSinCompletar((int) $etapa['caso_id'], $orden, $etapaId)) {
                    return ['ok' => false, 'error' => 'orden_saltado'];
                }
            }

            $fechaFin = trim((string) ($datos['fecha_fin'] ?? ''));
            if ($nuevoEstado === 'finalizado' && $fechaFin === '' && empty($etapa['fecha_fin'])) {
                return ['ok' => false, 'error' => 'fecha_real_requerida'];
            }

            $payload = [
                'nombre' => $datos['nombre'] ?? $etapa['nombre'],
                'descripcion' => $datos['descripcion'] ?? $etapa['descripcion'],
                'estado' => $nuevoEstado,
                'fecha_inicio' => $datos['fecha_inicio'] ?? $etapa['fecha_inicio'],
                'fecha_fin' => $fechaFin !== '' ? $fechaFin : ($etapa['fecha_fin'] ?? null),
                'fecha_estimada_fin' => $datos['fecha_estimada_fin'] ?? $etapa['fecha_estimada_fin'],
                'responsable_id' => $datos['responsable_id'] ?? $etapa['responsable_id'],
                'resultado' => $datos['resultado'] ?? $etapa['resultado'],
                'documentos_generados' => $datos['documentos_generados'] ?? $etapa['documentos_generados'],
                'observaciones' => $datos['observaciones'] ?? $etapa['observaciones']
            ];

            if (!$this->etapas->update($etapaId, $payload)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'UPDATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Actualizacion de etapa: {$payload['nombre']}",
                'datos_anteriores' => $this->sliceStageForAudit($etapa),
                'datos_nuevos' => $this->sliceStageForAudit(array_merge($etapa, $payload))
            ]);

            return ['ok' => true, 'caso_id' => (int) $etapa['caso_id']];
        });
    }

    public function cambiarEstadoEtapa(int $etapaId, string $nuevoEstado, ?string $fechaReal): array
    {
        return $this->inTransaction(function () use ($etapaId, $nuevoEstado, $fechaReal) {
            $etapa = $this->etapas->getById($etapaId);
            if (!$etapa) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            if (in_array($nuevoEstado, ['en_progreso', 'finalizado', 'bloqueado'], true)) {
                if ($this->etapas->existeEtapaPreviaSinCompletar((int) $etapa['caso_id'], (int) $etapa['orden'], $etapaId)) {
                    return ['ok' => false, 'error' => 'orden_saltado'];
                }
            }

            if ($nuevoEstado === 'finalizado') {
                $fechaFinal = trim((string) $fechaReal);
                if ($fechaFinal === '' && empty($etapa['fecha_fin'])) {
                    return ['ok' => false, 'error' => 'fecha_real_requerida'];
                }
                $fechaReal = $fechaFinal !== '' ? $fechaFinal : $etapa['fecha_fin'];
            } else {
                $fechaReal = null;
            }

            if (!$this->etapas->actualizarEstadoConFecha($etapaId, $nuevoEstado, $fechaReal)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'STATUS_CHANGE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Cambio de estado de etapa a: {$nuevoEstado}",
                'datos_anteriores' => ['estado' => $etapa['estado'] ?? null, 'fecha_fin' => $etapa['fecha_fin'] ?? null],
                'datos_nuevos' => ['estado' => $nuevoEstado, 'fecha_fin' => $fechaReal]
            ]);

            return ['ok' => true, 'caso_id' => (int) $etapa['caso_id']];
        });
    }

    public function eliminarEtapaLogico(int $etapaId): array
    {
        return $this->inTransaction(function () use ($etapaId) {
            $etapa = $this->etapas->getById($etapaId);
            if (!$etapa) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            if (!$this->etapas->delete($etapaId)) {
                return ['ok' => false, 'error' => 'db_error'];
            }

            $this->bitacora->log([
                'accion' => 'SOFT_DELETE',
                'modulo' => 'casos_rrll',
                'entidad' => 'etapa',
                'entidad_id' => $etapaId,
                'descripcion' => "Anulacion logica de etapa: {$etapa['nombre']}",
                'datos_anteriores' => ['estado' => $etapa['estado'] ?? null],
                'datos_nuevos' => ['estado' => 'cancelado']
            ]);

            return ['ok' => true, 'caso_id' => (int) $etapa['caso_id']];
        });
    }

    public function adjuntarDocumento(int $casoId, ?int $etapaId, array $documento): array
    {
        return $this->inTransaction(function () use ($casoId, $etapaId, $documento) {
            $caso = $this->casos->getById($casoId);
            if (!$caso) {
                return ['ok' => false, 'error' => 'no_encontrado'];
            }

            if ($etapaId !== null) {
                $etapa = $this->etapas->getById($etapaId);
                if (!$etapa || (int) $etapa['caso_id'] !== $casoId) {
                    return ['ok' => false, 'error' => 'etapa_invalida'];
                }
                if (!$this->etapas->agregarDocumentoEtapa($etapaId, $documento)) {
                    return ['ok' => false, 'error' => 'db_error'];
                }
            } else {
                if (!$this->casos->agregarDocumentoCaso($casoId, $documento)) {
                    return ['ok' => false, 'error' => 'db_error'];
                }
            }

            $this->bitacora->log([
                'accion' => 'CREATE',
                'modulo' => 'casos_rrll',
                'entidad' => 'documento_rrll',
                'entidad_id' => $casoId,
                'descripcion' => 'Adjunto de documento en expediente RRLL',
                'datos_nuevos' => [
                    'caso_id' => $casoId,
                    'etapa_id' => $etapaId,
                    'archivo' => $documento['nombre_original'] ?? null,
                    'ruta' => $documento['ruta'] ?? null
                ]
            ]);

            return ['ok' => true];
        });
    }

    private function normalizeCaseState(string $state): string
    {
        $normalized = strtolower(trim($state));
        $normalized = str_replace(['á', 'é', 'í', 'ó', 'ú'], ['a', 'e', 'i', 'o', 'u'], $normalized);
        $normalized = str_replace([' ', '-'], '_', $normalized);
        $normalized = preg_replace('/_+/', '_', $normalized) ?: '';

        $map = [
            'abierto' => 'activo',
            'activo' => 'activo',
            'en_progreso' => 'en_progreso',
            'en_proceso' => 'en_progreso',
            'en_tramite' => 'en_progreso',
            'suspendido' => 'suspendido',
            'suspendida' => 'suspendido',
            'cerrado' => 'cerrado',
            'finalizado' => 'cerrado',
            'finalizada' => 'cerrado',
            'archivado' => 'archivado',
            'anulado' => 'archivado',
            'inactivo' => 'archivado'
        ];

        return $map[$normalized] ?? $normalized;
    }

    private function inTransaction(callable $callback): array
    {
        try {
            if (!$this->db->inTransaction()) {
                $this->db->beginTransaction();
            }

            $result = $callback();
            if (!is_array($result)) {
                $result = ['ok' => false, 'error' => 'db_error'];
            }

            if (($result['ok'] ?? false) !== true) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                return $result;
            }

            if ($this->db->inTransaction()) {
                $this->db->commit();
            }
            return $result;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['ok' => false, 'error' => 'db_error', 'exception' => $e];
        }
    }

    private function sliceCaseForAudit(array $data): array
    {
        return [
            'numero_expediente' => $data['numero_expediente'] ?? null,
            'titulo' => $data['titulo'] ?? null,
            'categoria_id' => $data['categoria_id'] ?? null,
            'afiliado_id' => $data['afiliado_id'] ?? null,
            'prioridad' => $data['prioridad'] ?? null,
            'estado' => $data['estado'] ?? null,
            'responsable_actual' => $data['responsable_actual'] ?? null
        ];
    }

    private function sliceStageForAudit(array $data): array
    {
        return [
            'caso_id' => $data['caso_id'] ?? null,
            'nombre' => $data['nombre'] ?? null,
            'orden' => $data['orden'] ?? null,
            'estado' => $data['estado'] ?? null,
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'fecha_estimada_fin' => $data['fecha_estimada_fin'] ?? null,
            'responsable_id' => $data['responsable_id'] ?? null
        ];
    }
}
