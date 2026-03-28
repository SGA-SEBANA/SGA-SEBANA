<?php
ob_start();
?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Detalle de Solicitud #<?= htmlspecialchars($solicitud['id']) ?></h2>
            <a href="/SGA-SEBANA/public/vacaciones" class="btn btn-secondary shadow-sm">
                <i class="zmdi zmdi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>
                <?php 
                    if ($_GET['success'] === 'estado_actualizado') echo '¡El estado de la solicitud ha sido actualizado!';
                    if ($_GET['success'] === 'cancelada') echo '¡Has cancelado esta solicitud de vacaciones con éxito!';
                    if ($_GET['success'] === 'reprogramada') echo '¡Las vacaciones fueron reprogramadas y enviadas a revisión!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-header bg-white py-3 border-bottom">
                        <strong class="card-title text-primary"><i class="fa-solid fa-calendar-day me-2"></i>Información de las Vacaciones</strong>
                    </div>
                    <div class="card-body card-block px-4 py-4">
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Solicitante:</label>
                            <p class="h5 mt-1"><?= htmlspecialchars($solicitud['nombre_completo']) ?></p>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="font-weight-bold text-muted small text-uppercase">Fecha de Inicio:</label>
                                <p class="mt-1 text-dark font-weight-bold"><?= date('d/m/Y', strtotime($solicitud['fecha_inicio'])) ?></p>
                            </div>
                            <div class="col-6">
                                <label class="font-weight-bold text-muted small text-uppercase">Fecha de Fin:</label>
                                <p class="mt-1 text-dark font-weight-bold"><?= date('d/m/Y', strtotime($solicitud['fecha_fin'])) ?></p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Estado Actual:</label>
                            <div class="mt-2">
                                <?php
                                    $badgeClass = 'badge bg-secondary';
                                    if ($solicitud['estado'] === 'Pendiente') $badgeClass = 'badge bg-warning text-dark';
                                    elseif ($solicitud['estado'] === 'Aceptada') $badgeClass = 'badge bg-success';
                                    elseif ($solicitud['estado'] === 'Rechazada') $badgeClass = 'badge bg-danger';
                                ?>
                                <span class="<?= $badgeClass ?> px-3 py-2 rounded" style="font-size: 0.9rem;">
                                    <?= htmlspecialchars($solicitud['estado']) ?>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small text-uppercase">Motivo / Comentarios:</label>
                            <p class="text-justify mt-2 p-3 bg-light rounded border" style="line-height: 1.6;">
                                <?= !empty($solicitud['motivo']) ? nl2br(htmlspecialchars($solicitud['motivo'])) : '<i class="text-muted">Sin comentarios adicionales.</i>' ?>
                            </p>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top">
                            <small class="text-muted">Solicitud creada el <?= date('d/m/Y h:i A', strtotime($solicitud['fecha_creacion'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                
                <?php if ($nivel_acceso >= 50 && $solicitud['estado'] === 'Pendiente'): ?>
                    <div class="card border-primary shadow-sm mb-4">
                        <div class="card-header bg-primary text-white py-3">
                            <i class="zmdi zmdi-settings me-2"></i>Acciones de Jefatura
                        </div>
                        <div class="card-body py-4 text-center">
                            <p class="mb-3 text-muted small">Por favor, revise las fechas antes de dar una respuesta.</p>
                            <form action="/SGA-SEBANA/public/vacaciones/status/<?= $solicitud['id'] ?>" method="POST" class="d-inline">
                                <button type="submit" name="nuevo_estado" value="Aceptada" class="btn btn-success px-4 mx-1 shadow-sm">
                                    <i class="zmdi zmdi-check mr-1"></i> Aceptar
                                </button>
                                <button type="submit" name="nuevo_estado" value="Rechazada" class="btn btn-danger px-4 mx-1 shadow-sm">
                                    <i class="zmdi zmdi-close mr-1"></i> Rechazar
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($usuario_actual == $solicitud['usuario_id'] && in_array($solicitud['estado'], ['Pendiente', 'Aceptada'])): ?>
                    
                    <div class="card shadow-sm mb-4 border-info">
                        <div class="card-header bg-info text-white py-3">
                            <i class="zmdi zmdi-calendar-note me-2"></i>Reprogramar Vacaciones
                        </div>
                        <div class="card-body py-3">
                            <form action="/SGA-SEBANA/public/vacaciones/reschedule/<?= $solicitud['id'] ?>" method="POST">
                                <div class="form-group mb-2">
                                    <label class="small text-muted">Nueva Fecha de Inicio</label>
                                    <input type="date" id="nueva_fecha_inicio" name="nueva_fecha_inicio" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group mb-2">
                                    <label class="small text-muted">Nueva Fecha de Fin</label>
                                    <input type="date" id="nueva_fecha_fin" name="nueva_fecha_fin" class="form-control form-control-sm" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="small text-muted">Motivo de la reprogramación</label>
                                    <textarea name="nuevo_motivo" class="form-control form-control-sm" rows="2" required placeholder="Indique por qué cambia las fechas..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-info btn-sm btn-block shadow-sm text-white">
                                    <i class="zmdi zmdi-refresh-sync mr-1"></i> Enviar Reprogramación
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card shadow-sm border-danger">
                        <div class="card-body text-center py-4">
                            <h6 class="text-danger mb-3 font-weight-bold">¿Ya no tomarás estas vacaciones?</h6>
                            <form action="/SGA-SEBANA/public/vacaciones/cancel/<?= $solicitud['id'] ?>" method="POST" onsubmit="return confirm('¿Está seguro de que desea cancelar esta solicitud? Esta acción no se puede deshacer.');">
                                <button type="submit" class="btn btn-outline-danger btn-sm px-4 shadow-sm">
                                    <i class="zmdi zmdi-block mr-1"></i> Cancelar Solicitud
                                </button>
                            </form>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if (!($nivel_acceso >= 50 && $solicitud['estado'] === 'Pendiente') && !($usuario_actual == $solicitud['usuario_id'] && in_array($solicitud['estado'], ['Pendiente', 'Aceptada']))): ?>
                    <div class="card shadow-sm border-0 bg-light text-center py-5">
                        <i class="zmdi zmdi-lock zmdi-hc-3x text-muted mb-2"></i>
                        <p class="text-muted small">Esta solicitud ya está finalizada o no requiere acciones adicionales.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script>
    // Script para evitar fechas incorrectas en la reprogramación
    const reprogramarInicio = document.getElementById('nueva_fecha_inicio');
    const reprogramarFin = document.getElementById('nueva_fecha_fin');
    
    if (reprogramarInicio && reprogramarFin) {
        reprogramarInicio.addEventListener('change', function() {
            reprogramarFin.min = this.value;
        });
    }
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>