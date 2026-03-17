<?php
$title = "Administración de solicitudes de visita";
ob_start();
?>

<div class="row">
    <div class="col-md-12">

        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Administración de solicitudes</h2>
            <a href="/SGA-SEBANA/public/admin/request-calendar" class="au-btn au-btn-icon au-btn--green au-btn--small">
                <i class="fa-regular fa-calendar"></i> Calendario
            </a>
        </div>

        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Afiliado</th>
                        <th>Oficina</th>
                        <th>Empleado</th>
                        <th>Fecha</th>
                        <th>Motivo</th>
                        <th>Estado</th>
                        <th style="width:140px" class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitud)): ?>
                    <tr>
                        <td colspan="8" class="text-center p-4">
                            No hay solicitudes registradas
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($solicitud as $s): ?>
                    <tr class="tr-shadow">
                        <!-- Código con fondo gris -->
                        <td>
                            <span
                                style="background-color:#e9ecef; padding:3px 8px; border-radius:4px; font-weight:500;">
                                <?= htmlspecialchars($s['codigo_solicitud']) ?>
                            </span>
                        </td>

                        <td><?= htmlspecialchars($s['afiliado_nombre']) ?></td>
                        <td><?= htmlspecialchars($s['oficina_nombre']) ?></td>
                        <td><?= htmlspecialchars($s['nombre_empleado']) ?></td>

                        <!-- Fecha con resaltado si está reprogramada -->
                        <td>
                            <?php if (!empty($s['fecha_reprogramada'])): ?>
                            <span style="color:#ff9800;">
                                <?= htmlspecialchars($s['fecha_reprogramada']) ?> (Reprogramada)
                            </span>
                            <?php else: ?>
                            <?= $s['fecha_visita'] ? htmlspecialchars($s['fecha_visita']) : '—' ?>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($s['motivo']) ?></td>

                        <!-- Estado con fondo de color -->
                        <td>
                            <?php 
                                    $estado = strtolower($s['estado'] ?? '');
                                    switch($estado){
                                        case 'aprobada':
                                            $class = 'background-color:#28a745; color:#fff; padding:3px 8px; border-radius:4px;';
                                            break;
                                        case 'pendiente':
                                            $class = 'background-color:#ffc107; color:#212529; padding:3px 8px; border-radius:4px;';
                                            break;
                                        case 'cancelada':
                                        case 'rechazada':
                                            $class = 'background-color:#dc3545; color:#fff; padding:3px 8px; border-radius:4px;';
                                            break;
                                        default:
                                            $class = 'background-color:#6c757d; color:#fff; padding:3px 8px; border-radius:4px;';
                                    }
                                    ?>
                            <span style="<?= $class ?>"><?= ucfirst($s['estado'] ?? '—') ?></span>
                        </td>

                        <td class="text-center">
                            <div class="table-data-feature d-flex justify-content-center">
                                <?php if ($s['estado'] == 'pendiente'): ?>
                                <a href="/SGA-SEBANA/public/admin/visit-requests/accept/<?= $s['id'] ?>" class="item">

                                    <a href="/SGA-SEBANA/public/admin/visit-requests/reject/<?= $s['id'] ?>"
                                        class="item" title="Rechazar solicitud"
                                        onclick="return confirm('¿Desea rechazar esta solicitud?')">
                                        <i class="zmdi zmdi-close"></i>
                                    </a>
                                    <?php endif; ?>
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
require BASE_PATH . '/public/templates/base.html.php';
?>