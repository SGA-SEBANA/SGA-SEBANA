<?php
/**
 * Vista: Listado de Viaticos
 */
ob_start();
$esJefatura = $es_jefatura ?? false;
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Gestion de Viaticos</h2>
            <a href="/SGA-SEBANA/public/viaticos/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
                <i class="zmdi zmdi-plus me-2"></i>Nueva Solicitud
            </a>
        </div>

        <?php if (isset($success) && $success === 'creado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>Solicitud de viaticos creada con exito.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Consecutivo</th>
                        <th>Empleado(s)</th>
                        <th>Rango</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Alimentacion</th>
                        <th>Transporte</th>
                        <th>Total a Pagar</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($viaticos)): ?>
                        <?php foreach ($viaticos as $v): ?>
                            <tr class="tr-shadow">
                                <td><strong><?= htmlspecialchars($v['consecutivo']) ?></strong></td>
                                <td><?= !empty($v['empleados']) ? nl2br(htmlspecialchars($v['empleados'])) : 'N/D' ?></td>
                                <td>
                                    <?= !empty($v['fecha_inicio']) ? date('d/m/Y', strtotime($v['fecha_inicio'])) : 'N/D' ?>
                                    -
                                    <?= !empty($v['fecha_fin']) ? date('d/m/Y', strtotime($v['fecha_fin'])) : 'N/D' ?>
                                </td>
                                <td><?= date('d/m/Y h:i A', strtotime($v['creado_en'])) ?></td>
                                <td>
                                    <?php if ($v['estado'] === 'Borrador'): ?>
                                        <span class="status--process">Borrador</span>
                                    <?php else: ?>
                                        <span class="status--denied"><?= htmlspecialchars($v['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>C<?= number_format($v['monto_alimentacion'], 2) ?></td>
                                <td>C<?= number_format($v['monto_transporte'], 2) ?></td>
                                <td class="desc" style="color: #000000; font-weight: bold;">
                                    C<?= number_format($v['total_pagar'], 2) ?>
                                </td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/viaticos/show?id=<?= $v['id'] ?>" class="item" data-toggle="tooltip" data-placement="top" title="Ver Detalles">
                                            <i class="zmdi zmdi-eye"></i>
                                        </a>
                                        <a href="/SGA-SEBANA/public/viaticos/pdf?id=<?= $v['id'] ?>" target="_blank" class="item text-danger" data-toggle="tooltip" data-placement="top" title="Generar PDF">
                                            <i class="zmdi zmdi-collection-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr class="spacer"></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">No hay solicitudes de viaticos registradas aun.</td>
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
