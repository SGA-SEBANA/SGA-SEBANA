<?php
/**
 * Vista de Detalles del Caso RRLL
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalles del Caso: <?= htmlspecialchars($caso['numero_expediente']) ?></h2>
            <div>
                <a href="/SGA-SEBANA/public/casos-rrll/edit/<?= $caso['id'] ?>" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-edit"></i> Editar
                </a>
                <a href="/SGA-SEBANA/public/casos-rrll" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                    <i class="zmdi zmdi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> Actualización realizada correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Información General del Caso -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="zmdi zmdi-info"></i> Información General</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Estado:</strong><br>
                        <span class="badge bg-<?= $caso['estado'] === 'activo' ? 'success' : ($caso['estado'] === 'en_progreso' ? 'info' : 'secondary') ?>">
                            <?= ucfirst($caso['estado']) ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Prioridad:</strong><br>
                        <span class="badge bg-<?= $caso['prioridad'] === 'urgente' ? 'danger' : ($caso['prioridad'] === 'alta' ? 'warning' : 'info') ?>">
                            <?= ucfirst($caso['prioridad']) ?>
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Categoría:</strong><br>
                        <?= htmlspecialchars($caso['categoria_nombre'] ?? 'N/A') ?>
                    </div>
                    <div class="col-md-3">
                        <strong>Creado por:</strong><br>
                        <?= htmlspecialchars($caso['creado_por_nombre'] ?? 'N/A') ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles Principales -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="zmdi zmdi-assignment"></i> Descripción</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3"><strong><?= htmlspecialchars($caso['titulo']) ?></strong></p>
                        <p><?= nl2br(htmlspecialchars($caso['descripcion'])) ?></p>

                        <?php if (!empty($caso['hechos'])): ?>
                            <hr>
                            <h6><strong>Hechos Relevantes:</strong></h6>
                            <p><?= nl2br(htmlspecialchars($caso['hechos'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información de Empresa y Departamento -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="zmdi zmdi-building"></i> Información Laboral</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Empresa:</strong><br>
                                <?= htmlspecialchars($caso['empresa_involucrada'] ?? 'Banco Nacional') ?><br><br>
                                <strong>Departamento:</strong><br>
                                <?= htmlspecialchars($caso['departamento_afectado'] ?? 'No especificado') ?>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha del Incidente:</strong><br>
                                <?= $caso['fecha_incidente'] ? date('d/m/Y', strtotime($caso['fecha_incidente'])) : 'No especificada' ?><br><br>
                                <strong>Fecha de Apertura:</strong><br>
                                <?= date('d/m/Y', strtotime($caso['fecha_apertura'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-md-4">
                <!-- Responsable -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="zmdi zmdi-account-add"></i> Responsable</h6>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-2">
                            <strong><?= htmlspecialchars($caso['responsable_nombre'] ?? 'No asignado') ?></strong>
                        </p>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#cambiarResponsable">
                            Cambiar
                        </button>
                    </div>
                </div>

                <!-- Afiliado -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="zmdi zmdi-account"></i> Afiliado</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($caso['afiliado_nombre'])): ?>
                            <strong><?= htmlspecialchars($caso['afiliado_nombre']) ?></strong><br>
                            <small class="text-muted">C.I.: <?= htmlspecialchars($caso['afiliado_cedula']) ?></small>
                        <?php else: ?>
                            <p class="text-muted">No tiene afiliado asociado</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Progreso de Etapas -->
                <?php if (!empty($progreso) && $progreso['total'] > 0): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="zmdi zmdi-layers"></i> Progreso de Etapas</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="progress" style="height: 25px;">
                                    <?php
                                    $porcentaje = $progreso['total'] > 0 ? round(($progreso['completadas'] / $progreso['total']) * 100) : 0;
                                    ?>
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $porcentaje ?>%">
                                        <?= $porcentaje ?>%
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                <i class="zmdi zmdi-check-circle"></i> <?= $progreso['completadas'] ?> completadas de <?= $progreso['total'] ?> etapas
                            </small>
                        </div>
                    </div>

                    <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas" class="btn btn-primary btn-sm w-100 mb-2">
                        <i class="zmdi zmdi-layers"></i> Ver Etapas
                    </a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <small><i class="zmdi zmdi-info"></i> No hay etapas registradas</small>
                    </div>
                    <a href="/SGA-SEBANA/public/casos-rrll/<?= $caso['id'] ?>/etapas/create" class="btn btn-success btn-sm w-100 mb-2">
                        <i class="zmdi zmdi-plus"></i> Agregar Etapa
                    </a>
                <?php endif; ?>

                <!-- Acciones de estado -->
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="zmdi zmdi-settings"></i> Acciones</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($caso['estado'] !== 'archivado'): ?>
                            <button type="button" class="btn btn-warning btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cambiarEstado">
                                <i class="zmdi zmdi-refresh"></i> Cambiar Estado
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#archivarModal">
                                <i class="zmdi zmdi-archive"></i> Archivar
                            </button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-danger btn-sm w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="zmdi zmdi-delete"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Observaciones -->
        <?php if (!empty($caso['observaciones'])): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="zmdi zmdi-comment"></i> Observaciones</h6>
                </div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($caso['observaciones'])) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Resultado Final -->
        <?php if (!empty($caso['resultado_final'])): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="zmdi zmdi-check"></i> Resultado Final</h6>
                </div>
                <div class="card-body">
                    <?= nl2br(htmlspecialchars($caso['resultado_final'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
?>

<!-- Modal: Cambiar Estado del Caso -->
<div class="modal fade" id="cambiarEstado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/cambiar-estado/<?= $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Estado del Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevo_estado">Nuevo Estado:</label>
                        <select name="nuevo_estado" id="nuevo_estado" class="form-control" required>
                            <option value="activo">Activo</option>
                            <option value="en_progreso">En Progreso</option>
                            <option value="cerrado">Cerrado</option>
                            <option value="suspendido">Suspendido</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Archivar Caso -->
<div class="modal fade" id="archivarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/archivar/<?= $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Archivar Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>¿Deseas archivar este caso?</strong></p>
                    <p>Los casos archivados se mantienen en el sistema pero no aparecen en la lista activa.</p>
                    <div class="form-group">
                        <label for="resultado_final_modal">Resultado Final (opcional):</label>
                        <textarea name="resultado_final" id="resultado_final_modal" class="form-control" rows="3" placeholder="Resumen del resultado del caso..."></textarea>
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

<!-- Modal: Cambiar Responsable -->
<div class="modal fade" id="cambiarResponsable" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/cambiar-responsable/<?= $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Cambiar Responsable del Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nuevo_responsable">Nuevo Responsable:</label>
                        <select name="responsable_actual" id="nuevo_responsable" class="form-control" required>
                            <option value="">Sin asignar</option>
                            <?php foreach($usuarios as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= ($caso['responsable_actual'] == $user['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['nombre_completo']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar Responsable</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Eliminar Caso -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/SGA-SEBANA/public/casos-rrll/delete/<?= $caso['id'] ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Eliminar Caso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger"><strong>Esta acción es irreversible.</strong></p>
                    <p>Se eliminarán el caso y todas sus etapas asociadas del sistema.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Eliminar Permanentemente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>