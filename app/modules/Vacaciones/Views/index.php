<?php
ob_start();
$esJefatura = $es_jefatura ?? false;
?>

<div class="row mt-3">
    <div class="col-md-12">

        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Control de Vacaciones</h2>
            <a href="/SGA-SEBANA/public/vacaciones/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
                <i class="zmdi zmdi-plus me-2"></i>Nueva Solicitud
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>
                <?php
                    if ($_GET['success'] === 'creada') echo 'Solicitud de vacaciones enviada con exito.';
                    if ($_GET['success'] === 'estado_actualizado') echo 'El estado de la solicitud se actualizo correctamente.';
                    if ($_GET['success'] === 'cancelada') echo 'La solicitud fue cancelada exitosamente.';
                    if ($_GET['success'] === 'reprogramada') echo 'La solicitud fue reprogramada y enviada a revision.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i>
                <?php
                    if ($_GET['error'] === 'no_autorizado') echo 'No tienes permisos para realizar esta accion.';
                    if ($_GET['error'] === 'not_found') echo 'La solicitud que buscas no existe o fue eliminada.';
                    if ($_GET['error'] === 'db_error') echo 'Hubo un error al procesar la solicitud.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2 shadow-sm rounded border">
            <table class="table table-data2 bg-white mb-0">
                <thead class="bg-light">
                    <tr>
                        <th># ID</th>
                        <?php if ($esJefatura): ?>
                            <th>Solicitante</th>
                        <?php endif; ?>
                        <th>Periodo</th>
                        <th>Dias</th>
                        <th>Estado</th>
                        <th>Fecha de Envio</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitudes)): ?>
                        <tr>
                            <td colspan="<?= ($esJefatura) ? '7' : '6' ?>" class="text-center py-5">
                                <i class="zmdi zmdi-inbox zmdi-hc-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No hay solicitudes de vacaciones registradas.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($solicitudes as $s): ?>
                            <tr class="tr-shadow">
                               <td class="font-weight-bold text-dark"><strong>#<?= htmlspecialchars($s['id']) ?></strong></td>

                                <?php if ($esJefatura): ?>
                                    <td>
                                        <span class="text-dark"><?= htmlspecialchars($s['nombre_completo'] ?? 'Usuario') ?></span>
                                    </td>
                                <?php endif; ?>

                                <td>
                                    <i class="zmdi zmdi-calendar-note me-1 text-muted"></i>
                                    <?= !empty($s['fecha_inicio']) ? date('d/m/Y', strtotime($s['fecha_inicio'])) : '-' ?> <br>
                                    <small class="text-muted">al <?= !empty($s['fecha_fin']) ? date('d/m/Y', strtotime($s['fecha_fin'])) : '-' ?></small>
                                </td>

                                <td><?= (int)($s['cantidad_dias'] ?? 0) ?></td>

                                <td>
                                    <?php
                                        $estado = $s['estado'] ?? 'Pendiente';
                                        $badgeClass = 'badge bg-secondary';
                                        if ($estado === 'Pendiente') $badgeClass = 'badge bg-warning text-dark';
                                        elseif ($estado === 'Aceptada') $badgeClass = 'badge bg-success';
                                        elseif ($estado === 'Rechazada') $badgeClass = 'badge bg-danger';
                                        elseif ($estado === 'Cancelada') $badgeClass = 'badge bg-dark';
                                        elseif ($estado === 'En Revision' || $estado === 'Reprogramada') $badgeClass = 'badge bg-info text-dark';
                                    ?>
                                    <span class="<?= $badgeClass ?> px-3 py-2 rounded" style="font-size: 0.85rem;">
                                        <?= htmlspecialchars($estado) ?>
                                    </span>
                                </td>

                                <td><?= !empty($s['fecha_creacion']) ? date('d/m/Y h:i A', strtotime($s['fecha_creacion'])) : '-' ?></td>

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
            <div class="mt-3 d-flex justify-content-center gap-2">
<?php if (!empty($totalPaginas) && $totalPaginas > 1): ?>
<div class="mt-3 d-flex justify-content-center gap-2">
    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
           class="btn btn-sm <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
