<?php
$title = "Solicitudes de visitas";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Solicitudes</h2>
            <div class="d-flex">

                <a href="/SGA-SEBANA/public/visit-requests/create"
                    class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="fa-regular fa-paper-plane"></i> Realizar Solicitud
                </a>


            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Oficina</th>
                        <th>Fecha</th>
                        <th>Estado</th>

                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($solicitud)): ?>
                    <tr>
                        <td colspan="8" class="text-center p-4">
                            <p>No hay solicitudes realizadas.</p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($solicitud as $miembro): ?>
                    <tr class="tr-shadow">

                        <td>
                            <span class="block-email"><?= htmlspecialchars($miembro['codigo_solicitud']) ?></span>
                        </td>

                        <td><?= htmlspecialchars($miembro['oficina_nombre']) ?></td>

                        <td>
                            <?php if (!empty($miembro['fecha_reprogramada'])): ?>
                            <span style="color:#ff9800;">
                                <?= htmlspecialchars($miembro['fecha_reprogramada']) ?> (Reprogramada)
                            </span>
                            <?php else: ?>
                            <?= $miembro['fecha_visita'] ? htmlspecialchars($miembro['fecha_visita']) : '—' ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php 
                                $estado = strtolower($miembro['estado'] ?? '');
                                switch($estado){
                                    case 'aprobada':
                                        $class = 'status-badge status-aprobada';
                                        break;
                                    case 'pendiente':
                                        $class = 'status-badge status-pendiente';
                                        break;
                                    case 'cancelada':
                                        $class = 'status-badge status-cancelada';
                                        break;
                                    default:
                                        $class = 'status-badge status-desconocido';
                                }
                                ?>
                            <span class="<?= $class ?>"><?= ucfirst($miembro['estado'] ?? '—') ?></span>
                        </td>

                        <td>
                            <div class="table-data-feature">
                                <a href="/SGA-SEBANA/public/visit-requests/<?= $miembro['id'] ?>/reschedule"
                                    class="item" data-toggle="tooltip" title="Re-Programar">
                                    <i class="fa-regular fa-calendar-xmark"></i>
                                </a>

                                <a href="/SGA-SEBANA/public/visit-requests/<?= $miembro['id'] ?>/cancel" class="item"
                                    onclick="return confirm('¿Cancelar esta solicitud?')">
                                    <i class="fa-solid fa-ban"></i>
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
require BASE_PATH . '/public/templates/base.html.php';