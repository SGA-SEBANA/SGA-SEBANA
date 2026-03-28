<?php
ob_start();
?>

<div class="row mt-3">
    <div class="col-md-12">
        
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Control de Vacaciones</h2>
            <a href="/SGA-SEBANA/public/vacaciones/create" class="btn btn-primary shadow-sm">
                <i class="zmdi zmdi-plus me-2"></i>Nueva Solicitud
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>
                <?php 
                    if ($_GET['success'] === 'creada') echo '¡Solicitud de vacaciones enviada con éxito!';
                    if ($_GET['success'] === 'estado_actualizado') echo '¡El estado de la solicitud se actualizó correctamente!';
                    if ($_GET['success'] === 'cancelada') echo '¡La solicitud fue cancelada exitosamente!';
                    if ($_GET['success'] === 'reprogramada') echo '¡La solicitud ha sido reprogramada y enviada a revisión!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i>
                <?php 
                    if ($_GET['error'] === 'no_autorizado') echo 'No tienes permisos para realizar esta acción.';
                    if ($_GET['error'] === 'not_found') echo 'La solicitud que buscas no existe o fue eliminada.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2 shadow-sm rounded border">
            <table class="table table-data2 bg-white mb-0">
                <thead class="bg-light">
                    <tr>
                        <th># ID</th>
                        <?php if ($nivel_acceso >= 50): ?>
                            <th>Solicitante</th>
                        <?php endif; ?>
                        <th>Período</th>
                        <th>Estado</th>
                        <th>Fecha de Envío</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="<?= ($nivel_acceso >= 50) ? '6' : '5' ?>" class="text-center py-5">
                                <i class="zmdi zmdi-inbox zmdi-hc-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay solicitudes de vacaciones registradas.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr class="tr-shadow">
                                <td class="font-weight-bold text-primary">#<?= htmlspecialchars($s['id']) ?></td>
                                
                                <?php if ($nivel_acceso >= 50): ?>
                                    <td>
                                        <span class="block-email text-dark"><?= htmlspecialchars($s['nombre_completo'] ?? 'Usuario') ?></span>
                                    </td>
                                <?php endif; ?>
                                
                                <td>
                                    <i class="zmdi zmdi-calendar-note me-1 text-muted"></i>
                                    <?= date('d/m/Y', strtotime($s['fecha_inicio'])) ?> <br> 
                                    <small class="text-muted">al <?= date('d/m/Y', strtotime($s['fecha_fin'])) ?></small>
                                </td>
                                
                                <td>
                                    <?php
                                        $badgeClass = 'badge bg-secondary'; // Por defecto (Cancelada)
                                        if ($s['estado'] === 'Pendiente') $badgeClass = 'badge bg-warning text-dark';
                                        elseif ($s['estado'] === 'Aceptada') $badgeClass = 'badge bg-success';
                                        elseif ($s['estado'] === 'Rechazada') $badgeClass = 'badge bg-danger';
                                    ?>
                                    <span class="<?= $badgeClass ?> px-3 py-2 rounded" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($s['estado']) ?>
                                    </span>
                                </td>
                                
                                <td><?= date('d/m/Y h:i A', strtotime($s['fecha_creacion'])) ?></td>
                                
                                <td>
                                    <div class="table-data-feature justify-content-end">
                                        <a href="/SGA-SEBANA/public/vacaciones/show/<?= $s['id'] ?>" class="item btn btn-outline-info btn-sm" data-toggle="tooltip" data-placement="top" title="Ver Detalle">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>