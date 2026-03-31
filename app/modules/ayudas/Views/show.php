<?php
/**
 * Vista: Detalle de Solicitud (SGA-SEBANA)
 */
ob_start();
?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Detalle de Solicitud #<?= htmlspecialchars($ayuda['id']) ?></h2>
            <a href="/SGA-SEBANA/public/ayudas" class="btn btn-secondary shadow-sm">
                <i class="zmdi zmdi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>
                <?php 
                    if ($_GET['success'] === 'estado_actualizado') echo '¡Estado actualizado correctamente!';
                    if ($_GET['success'] === 'evidencia_agregada') echo '¡Evidencia adjuntada con éxito!';
                    if ($_GET['success'] === 'cancelacion_enviada') echo '¡Solicitud de cancelación enviada correctamente!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i>
                <?php 
                    if ($_GET['error'] === 'invalid_file') echo '<strong>Error:</strong> El archivo adjunto debe ser PDF, JPG, JPEG o PNG y pesar un máximo de 5MB.';
                    if ($_GET['error'] === 'cancel_error') echo 'Error al procesar la cancelación. Intente nuevamente.';
                    if ($_GET['error'] === 'upload_failed') echo 'Ocurrió un error al subir el archivo. Intente nuevamente.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header py-3">
                        <strong class="card-title">Información</strong> de la Solicitud
                    </div>
                    <div class="card-body card-block px-4 py-4">
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Solicitante:</label>
                            <p class="h5 mt-1"><?= htmlspecialchars($ayuda['nombre_completo']) ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Fecha de Solicitud:</label>
                            <p class="mt-1 text-dark"><?= date('d/m/Y h:i A', strtotime($ayuda['fecha_solicitud'])) ?></p>
                        </div>
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Monto Solicitado:</label>
                            <p class="desc mt-1" style="color: #001B71; font-weight: 800; font-size: 1.4rem;">
                                ₡<?= number_format($ayuda['monto_solicitado'], 2) ?>
                            </p>
                        </div>
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Estado Actual:</label>
                            <div class="mt-2">
                                <?php
                                    $badgeClass = 'status--denied';
                                    if ($ayuda['estado'] === 'Pendiente') $badgeClass = 'status--process';
                                    elseif ($ayuda['estado'] === 'Aprobada') $badgeClass = 'status--green" style="color: #00ad5f;';
                                    elseif ($ayuda['estado'] === 'Cancelación Solicitada') $badgeClass = 'status--process" style="color: #ff9800; background: #fff3e0;';
                                ?>
                                <span class="<?= $badgeClass ?> px-3 py-1 rounded" style="font-size: 0.9rem;"><?= htmlspecialchars($ayuda['estado']) ?></span>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Motivo:</label>
                            <p class="text-justify mt-2 p-3 bg-light rounded border" style="line-height: 1.6;">
                                <?= nl2br(htmlspecialchars($ayuda['motivo'])) ?>
                            </p>
                        </div>

                        <?php if ($ayuda['estado'] === 'Pendiente' && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $ayuda['usuario_id']): ?>
                            <div class="mt-4 p-4 border rounded bg-light border-danger shadow-sm">
                                <h6 class="text-danger font-weight-bold mb-3">Solicitar Cancelación de Ayuda</h6>
                                <form action="/SGA-SEBANA/public/ayudas/cancel/<?= $ayuda['id'] ?>" method="POST">
                                    <div class="form-group mb-3">
                                        <textarea name="motivo_cancelacion" class="form-control" rows="3" placeholder="Indique el motivo de la cancelación..." required></textarea>
                                    </div>
                                    <div class="text-right">
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-4">
                                            Enviar Solicitud de Cancelación
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (isset($_SESSION['user']['nivel_acceso']) && $_SESSION['user']['nivel_acceso'] >= 50): ?>
                    <div class="card border-primary shadow-sm mb-4">
                        <div class="card-header bg-dark text-white py-3">
                            <i class="zmdi zmdi-settings me-2"></i>Acciones de Administración
                        </div>
                        <div class="card-body py-4 text-center">
                            <form action="/SGA-SEBANA/public/ayudas/status/<?= $ayuda['id'] ?>" method="POST" class="d-inline">
                                <?php if ($ayuda['estado'] === 'Pendiente' || $ayuda['estado'] === 'Cancelación Solicitada'): ?>
                                    <button type="submit" name="nuevo_estado" value="Aprobada" class="btn btn-success px-4 mx-2 shadow-sm">
                                        <i class="zmdi zmdi-check mr-1"></i> Aprobar
                                    </button>
                                    <button type="submit" name="nuevo_estado" value="Rechazada" class="btn btn-danger px-4 mx-2 shadow-sm">
                                        <i class="zmdi zmdi-close mr-1"></i> Rechazar
                                    </button>
                                    <?php if ($ayuda['estado'] === 'Cancelación Solicitada'): ?>
                                        <button type="submit" name="nuevo_estado" value="Cancelada" class="btn btn-warning px-4 mx-2 shadow-sm text-dark">
                                            <i class="zmdi zmdi-block mr-1"></i> Confirmar Cancelación
                                        </button>
                                    <?php endif; ?>
                                <?php elseif ($ayuda['estado'] === 'Aprobada'): ?>
                                    <button type="submit" name="nuevo_estado" value="Cancelada" class="btn btn-warning px-4 shadow-sm text-dark">
                                        <i class="zmdi zmdi-refresh-sync mr-1"></i> Reevaluar y Cancelar
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted font-italic font-weight-bold">Solicitud Finalizada</span>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0">
                    <div class="card-header py-3 bg-white border-bottom">
                        <i class="zmdi zmdi-collection-item me-2 text-primary"></i><strong>Evidencias</strong> Adjuntas
                    </div>
                    <div class="card-body px-4 py-4">
                        <?php if (!empty($evidencias)): ?>
                            <div class="list-group list-group-flush mb-4 shadow-sm border rounded">
                                <?php foreach ($evidencias as $e): ?>
                                    <div class="list-group-item px-3 py-3 border-bottom">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div style="max-width: 75%; overflow: hidden;">
                                                <h6 class="mb-1 text-primary text-truncate" title="<?= htmlspecialchars($e['nombre_archivo']) ?>">
                                                    <i class="zmdi zmdi-file-text me-2"></i>
                                                    <?= htmlspecialchars($e['nombre_archivo']) ?>
                                                </h6>
                                                
                                                <small class="text-muted d-block mt-1">
                                                    Subido por: <strong><?= htmlspecialchars($e['nombre_completo']) ?></strong>
                                                </small>
                                                <small class="text-muted d-block">
                                                    Fecha: <?= date('d/m/y H:i', strtotime($e['fecha_carga'])) ?>
                                                </small>
                                                <small class="badge badge-light border text-dark mt-1 font-weight-normal">
                                                    Estado al subir: <?= htmlspecialchars($e['estado_solicitud_al_subir']) ?>
                                                </small>
                                            </div>
                                            <div class="table-data-feature">
                                                <a href="/SGA-SEBANA/public/ayudas/archivo/<?= $e['id'] ?>" target="_blank" class="item" title="Ver">
                                                    <i class="zmdi zmdi-eye text-info"></i>
                                                </a>
                                                <a href="/SGA-SEBANA/public/ayudas/archivo/<?= $e['id'] ?>?download=1" class="item text-danger" title="Descargar">
                                                    <i class="zmdi zmdi-download text-danger"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 mb-4 bg-light rounded border border-dashed">
                                <i class="zmdi zmdi-info-outline zmdi-hc-2x text-muted mb-2"></i>
                                <p class="text-muted small">No hay archivos adjuntos.</p>
                            </div>
                        <?php endif; ?>

                        <?php if ($ayuda['estado'] === 'Pendiente'): ?>
                            <div class="p-4 bg-light border rounded shadow-sm" style="overflow: hidden;">
                                <h6 class="mb-3 font-weight-bold"><i class="zmdi zmdi-plus-circle-o mr-2"></i>Añadir Nueva Evidencia</h6>
                                <form action="/SGA-SEBANA/public/ayudas/evidence/<?= $ayuda['id'] ?>" method="POST" enctype="multipart/form-data">
                                    <div class="form-group mb-3" style="max-width: 100%; overflow: hidden;">
                                        <input type="file" name="nueva_evidencia" class="form-control-file text-truncate shadow-none" style="max-width: 100%; font-size: 0.85rem;" required>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-block shadow-sm py-2">
                                        <i class="zmdi zmdi-upload me-2"></i>Subir Archivo
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>