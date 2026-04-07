<?php
/**
 * Dashboard de expediente RRLL
 */
ob_start();

$estadoLabels = [
    'activo' => 'Abierto',
    'en_progreso' => 'En tramite',
    'suspendido' => 'Suspendido',
    'cerrado' => 'Cerrado',
    'archivado' => 'Archivado'
];
$estadoBadge = [
    'activo' => 'success',
    'en_progreso' => 'info',
    'suspendido' => 'warning',
    'cerrado' => 'primary',
    'archivado' => 'secondary'
];
$estadoActual = (string) ($caso['estado'] ?? 'activo');
$estadoTexto = $estadoLabels[$estadoActual] ?? ucfirst($estadoActual);
$badgeEstado = $estadoBadge[$estadoActual] ?? 'secondary';
$etapaActualNombre = (string) ($etapaActual['nombre'] ?? 'Sin etapa activa');
$etapaActualEstado = (string) ($etapaActual['estado'] ?? 'sin_etapa');
$ultimaActuacion = !empty($historial[0]['fecha_creacion'])
    ? date('d/m/Y H:i', strtotime((string) $historial[0]['fecha_creacion']))
    : 'Sin actividad';
$porcentaje = (!empty($progreso['total']) && (int) $progreso['total'] > 0)
    ? (int) round((((int) $progreso['completadas']) / ((int) $progreso['total'])) * 100)
    : 0;
$puedeGestionar = !in_array($estadoActual, ['cerrado', 'archivado'], true);
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-3">
            <h2 class="title-1">Expediente RRLL: <?= htmlspecialchars((string) $caso['numero_expediente']) ?></h2>
            <div>
                <a href="/SGA-SEBANA/public/casos-rrll" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-arrow-left"></i> Volver
                </a>
                <a href="/SGA-SEBANA/public/casos-rrll/edit/<?= (int) $caso['id'] ?>" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-edit"></i> Editar Datos Fijos
                </a>
            </div>
        </div>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                $s = (string) $_GET['success'];
                if ($s === 'estado_actualizado') echo 'Estado actualizado correctamente.';
                elseif ($s === 'responsable_actualizado') echo 'Responsable actualizado correctamente.';
                elseif ($s === 'documento_subido') echo 'Documento adjuntado correctamente.';
                else echo 'Operacion realizada correctamente.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars((string) $_GET['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <div class="text-muted small">Estado General</div>
                        <span class="badge bg-<?= $badgeEstado ?>"><?= $estadoTexto ?></span>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">Prioridad</div>
                        <strong><?= htmlspecialchars(ucfirst((string) ($caso['prioridad'] ?? 'media'))) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Afiliado</div>
                        <strong><?= htmlspecialchars((string) ($caso['afiliado_nombre'] ?? 'No asociado')) ?></strong>
                    </div>
                    <div class="col-md-2">
                        <div class="text-muted small">Etapa Actual</div>
                        <strong><?= htmlspecialchars($etapaActualNombre) ?></strong>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Ultima Actuacion</div>
                        <strong><?= htmlspecialchars($ultimaActuacion) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><strong>Resumen y Hechos Relevantes</strong></div>
                    <div class="card-body">
                        <h5 class="mb-2"><?= htmlspecialchars((string) $caso['titulo']) ?></h5>
                        <p class="mb-3"><?= nl2br(htmlspecialchars((string) $caso['descripcion'])) ?></p>
                        <?php if (!empty($caso['hechos'])): ?>
                            <hr>
                            <h6>Hechos Relevantes</h6>
                            <p class="mb-0"><?= nl2br(htmlspecialchars((string) $caso['hechos'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <ul class="nav nav-tabs card-header-tabs" id="rrllTabs" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-etapas" type="button">Etapas</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-historial" type="button">Historial</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-documentos" type="button">Documentos</button></li>
                        </ul>
                    </div>
                    <div class="card-body tab-content">
                        <div class="tab-pane fade show active" id="tab-etapas">
                            <?php if (!empty($etapas)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($etapas as $etapa): ?>
                                        <?php
                                        $estadoEtapa = (string) ($etapa['estado'] ?? 'pendiente');
                                        $badgeEtapa = [
                                            'pendiente' => 'secondary',
                                            'en_progreso' => 'primary',
                                            'finalizado' => 'success',
                                            'bloqueado' => 'danger',
                                            'cancelado' => 'dark'
                                        ][$estadoEtapa] ?? 'secondary';
                                        ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-start">
                                            <div>
                                                <div><strong>#<?= (int) $etapa['orden'] ?> - <?= htmlspecialchars((string) $etapa['nombre']) ?></strong></div>
                                                <div class="small text-muted">
                                                    Inicio: <?= !empty($etapa['fecha_inicio']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $etapa['fecha_inicio']))) : '-' ?> |
                                                    Estimada: <?= !empty($etapa['fecha_estimada_fin']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $etapa['fecha_estimada_fin']))) : '-' ?> |
                                                    Real: <?= !empty($etapa['fecha_fin']) ? htmlspecialchars(date('d/m/Y', strtotime((string) $etapa['fecha_fin']))) : '-' ?>
                                                </div>
                                            </div>
                                            <span class="badge bg-<?= $badgeEtapa ?>"><?= htmlspecialchars(str_replace('_', ' ', $estadoEtapa)) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No hay etapas registradas.</p>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="tab-historial">
                            <?php if (!empty($historial)): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Accion</th>
                                                <th>Entidad</th>
                                                <th>Detalle</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($historial as $log): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $log['fecha_creacion']))) ?></td>
                                                    <td><?= htmlspecialchars((string) ($log['accion'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars((string) ($log['entidad'] ?? '')) ?></td>
                                                    <td><?= htmlspecialchars((string) ($log['descripcion'] ?? '')) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted mb-0">No hay eventos de bitacora para este expediente.</p>
                            <?php endif; ?>
                        </div>

                        <div class="tab-pane fade" id="tab-documentos">
                            <h6 class="mb-2">Adjuntos del Expediente</h6>
                            <?php if (!empty($documentosCaso)): ?>
                                <ul class="list-group mb-3">
                                    <?php foreach ($documentosCaso as $doc): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars((string) ($doc['nombre_original'] ?? 'Documento')) ?></span>
                                            <?php if (!empty($doc['ruta'])): ?>
                                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="/SGA-SEBANA/<?= htmlspecialchars((string) $doc['ruta']) ?>">Ver</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Sin ruta</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No hay adjuntos de expediente.</p>
                            <?php endif; ?>

                            <h6 class="mb-2">Adjuntos por Etapa</h6>
                            <?php if (!empty($documentosEtapas)): ?>
                                <ul class="list-group">
                                    <?php foreach ($documentosEtapas as $doc): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <?= htmlspecialchars((string) ($doc['nombre_original'] ?? 'Documento')) ?>
                                                <small class="text-muted">(<?= htmlspecialchars((string) ($doc['etapa_nombre'] ?? 'Etapa')) ?>)</small>
                                            </span>
                                            <?php if (!empty($doc['ruta'])): ?>
                                                <a class="btn btn-sm btn-outline-primary" target="_blank" href="/SGA-SEBANA/<?= htmlspecialchars((string) $doc['ruta']) ?>">Ver</a>
                                            <?php else: ?>
                                                <span class="text-muted small">Sin ruta</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted mb-0">No hay adjuntos en etapas.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light"><strong>Panel de Control</strong></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="small text-muted">Responsable actual</div>
                            <strong><?= htmlspecialchars((string) ($caso['responsable_nombre'] ?? 'No asignado')) ?></strong>
                        </div>
                        <div class="mb-3">
                            <div class="small text-muted">Progreso</div>
                            <div class="progress" style="height: 22px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje ?>%;">
                                    <?= $porcentaje ?>%
                                </div>
                            </div>
                            <small class="text-muted"><?= (int) ($progreso['completadas'] ?? 0) ?> de <?= (int) ($progreso['total'] ?? 0) ?> etapas finalizadas</small>
                        </div>
                        <div class="d-grid gap-2">
                            <?php if ($puedeGestionar): ?>
                                <a href="/SGA-SEBANA/public/casos-rrll/<?= (int) $caso['id'] ?>/etapas/create" class="btn btn-success btn-sm">
                                    <i class="zmdi zmdi-plus"></i> Nueva Etapa
                                </a>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cambiarResponsable">
                                    <i class="zmdi zmdi-account-box"></i> Cambiar Responsable
                                </button>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#adjuntarDocumentoModal">
                                    <i class="zmdi zmdi-attachment"></i> Adjuntar Documento
                                </button>
                                <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#cambiarEstado">
                                    <i class="zmdi zmdi-refresh"></i> Cambiar Estado
                                </button>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#cerrarModal">
                                    <i class="zmdi zmdi-check"></i> Cerrar Expediente
                                </button>
                                <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#archivarModal">
                                    <i class="zmdi zmdi-archive"></i> Archivar Expediente
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); ?>

<div class="modal fade" id="adjuntarDocumentoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/<?= (int) $caso['id'] ?>/documentos/upload" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Adjuntar Documento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="etapa_id" class="form-label">Asociar a etapa (opcional)</label>
                        <select class="form-control" name="etapa_id" id="etapa_id">
                            <option value="">Documento del expediente general</option>
                            <?php foreach ($etapas as $etapa): ?>
                                <option value="<?= (int) $etapa['id'] ?>">#<?= (int) $etapa['orden'] ?> - <?= htmlspecialchars((string) $etapa['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="documento" class="form-label">Archivo</label>
                        <input type="file" class="form-control" id="documento" name="documento" required>
                        <small class="text-muted">Permitidos: PDF, JPG, PNG, DOC, DOCX. Max 10MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adjuntar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cambiarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/cambiar-estado/<?= (int) $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Expediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="nuevo_estado" class="form-label">Nuevo estado</label>
                    <select name="nuevo_estado" id="nuevo_estado" class="form-control" required>
                        <?php if ($estadoActual === 'activo'): ?>
                            <option value="en_progreso">En tramite</option>
                            <option value="suspendido">Suspendido</option>
                            <option value="archivado">Archivado</option>
                        <?php elseif ($estadoActual === 'en_progreso'): ?>
                            <option value="suspendido">Suspendido</option>
                            <option value="archivado">Archivado</option>
                        <?php elseif ($estadoActual === 'suspendido'): ?>
                            <option value="en_progreso">En tramite</option>
                            <option value="archivado">Archivado</option>
                        <?php else: ?>
                            <option value="archivado">Archivado</option>
                        <?php endif; ?>
                    </select>
                    <small class="text-muted">El cierre formal se realiza con la accion "Cerrar Expediente".</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cerrarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/cambiar-estado/<?= (int) $caso['id'] ?>">
                <input type="hidden" name="nuevo_estado" value="cerrado">
                <div class="modal-header">
                    <h5 class="modal-title">Cerrar Expediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Esta accion valida que no existan etapas activas.</p>
                    <div class="form-group">
                        <label for="resultado_final_modal">Resultado final <span class="text-danger">*</span></label>
                        <textarea name="resultado_final" id="resultado_final_modal" class="form-control" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cerrar Expediente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="archivarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/archivar/<?= (int) $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Archivar Expediente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>El expediente quedara fuera del flujo operativo.</p>
                    <div class="form-group">
                        <label for="resultado_final_archivo">Observacion final (opcional)</label>
                        <textarea name="resultado_final" id="resultado_final_archivo" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Archivar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="cambiarResponsable" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/cambiar-responsable/<?= (int) $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Responsable</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="nuevo_responsable" class="form-label">Responsable</label>
                    <select name="responsable_actual" id="nuevo_responsable" class="form-control">
                        <option value="">Sin asignar</option>
                        <?php foreach ($usuarios as $user): ?>
                            <option value="<?= (int) $user['id'] ?>" <?= ((int) ($caso['responsable_actual'] ?? 0) === (int) $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars((string) $user['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php'; ?>
