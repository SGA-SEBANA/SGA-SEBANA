<?php
/**
 * Vista: Listado de Ayudas Economicas
 */
ob_start();
?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Gestion de Ayudas Economicas</h2>
            <a href="/SGA-SEBANA/public/ayudas/create" class="btn btn-primary shadow-sm">
                <i class="zmdi zmdi-plus me-2"></i>Nueva Solicitud
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>
                <?php
                    if ($_GET['success'] === 'creado') echo 'Solicitud enviada con exito.';
                    if ($_GET['success'] === 'cancelacion_enviada') echo 'Su solicitud de cancelacion fue recibida.';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Solicitante</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Monto Solicitado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ayudas)): ?>
                        <?php foreach ($ayudas as $a): ?>
                            <tr class="tr-shadow">
                                <td><strong>#<?= htmlspecialchars($a['id']) ?></strong></td>
                                <td style="color: #000;"><?= htmlspecialchars($a['nombre_completo']) ?></td>
                                <td><?= date('d/m/Y h:i A', strtotime($a['fecha_solicitud'])) ?></td>
                                <td>
                                    <?php if ($a['estado'] === 'Pendiente'): ?>
                                        <span class="status--process">Pendiente</span>
                                    <?php elseif ($a['estado'] === 'Aprobada'): ?>
                                        <span class="status--green" style="color: #00ad5f;">Aprobada</span>
                                    <?php elseif ($a['estado'] === 'Cancelacion Solicitada' || $a['estado'] === 'Cancelación Solicitada'): ?>
                                        <span class="status--process" style="color: #ff9800; background: #fff3e0;">Cancelacion Solicitada</span>
                                    <?php else: ?>
                                        <span class="status--denied"><?= htmlspecialchars($a['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="desc" style="color: #001B71; font-weight: 800; font-size: 1.05rem;">
                                    C<?= number_format($a['monto_solicitado'], 2) ?>
                                </td>
                                <td>
                                    <div class="table-data-feature justify-content-center">
                                        <a href="/SGA-SEBANA/public/ayudas/show/<?= $a['id'] ?>" class="item shadow-sm" data-toggle="tooltip" data-placement="top" title="Ver Detalles">
                                            <i class="zmdi zmdi-eye text-primary"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="zmdi zmdi-info-outline zmdi-hc-2x mb-2"></i><br>
                                No hay solicitudes de ayuda registradas aun.
                            </td>
                        </tr>
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
