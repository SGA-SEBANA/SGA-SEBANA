<?php
/**
 * Vista: Listado de Viáticos (Data Table 2 con Espaciado Profesional y PDF Conectado)
 */
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Gestión de Viáticos</h2>
            <a href="/SGA-SEBANA/public/viaticos/create" class="btn btn-primary">
                <i class="zmdi zmdi-plus me-2"></i>Nueva Solicitud
            </a>
        </div>

        <?php if (isset($success) && $success === 'creado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle me-2"></i>¡Solicitud de viáticos creada con éxito!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Consecutivo</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Alimentación</th>
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
                                <td><?= date('d/m/Y h:i A', strtotime($v['creado_en'])) ?></td>
                                <td>
                                    <?php if($v['estado'] === 'Borrador'): ?>
                                        <span class="status--process">Borrador</span>
                                    <?php else: ?>
                                        <span class="status--denied"><?= htmlspecialchars($v['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>₡<?= number_format($v['monto_alimentacion'], 2) ?></td>
                                <td>₡<?= number_format($v['monto_transporte'], 2) ?></td>
                                <td class="desc" style="color: #001B71; font-weight: bold;">
                                    ₡<?= number_format($v['total_pagar'], 2) ?>
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
                            <td colspan="7" class="text-center">No hay solicitudes de viáticos registradas aún.</td>
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