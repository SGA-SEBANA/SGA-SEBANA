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
                    <i class="fa-regular fa-paper-plane"></i>
                    Realizar Solicitud
                </a>
            </div>
        </div>


        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">

            <table class="table table-data2">

                <thead>
                    <tr>
                        <th>Código</th>

                        <?php if (!empty($es_jefatura)): ?>
                            <th>Afiliado</th>
                        <?php endif; ?>

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
                            No hay solicitudes realizadas.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($solicitud as $miembro): ?>

                        <tr class="tr-shadow">

                            <!-- Código -->
                            <td>
                                <?= htmlspecialchars($miembro['codigo_solicitud']) ?>
                            </td>


                            <!-- Afiliado (solo jefatura) -->
                            <?php if (!empty($es_jefatura)): ?>

                                <td>
                                    <?= htmlspecialchars($miembro['afiliado_nombre'] ?? '') ?>
                                </td>

                            <?php endif; ?>


                            <!-- Oficina -->
                            <td>
                                <?= htmlspecialchars($miembro['oficina_nombre']) ?>
                            </td>


                            <!-- Fecha -->
                            <td>

                                <?php if (!empty($miembro['fecha_reprogramada'])): ?>

                                    <span style="color:#ff9800;">
                                        <?= htmlspecialchars($miembro['fecha_reprogramada']) ?>
                                        (Reprogramada)
                                    </span>

                                <?php else: ?>

                                    <?= !empty($miembro['fecha_visita'])
                                        ? htmlspecialchars($miembro['fecha_visita'])
                                        : '—' ?>

                                <?php endif; ?>

                            </td>


                            <!-- Estado -->
                            <td>

                                <?php

                                $estado = strtolower($miembro['estado'] ?? '');

                                switch ($estado)
                                {
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

                                <span class="<?= $class ?>">
                                    <?= ucfirst($miembro['estado'] ?? '—') ?>
                                </span>

                            </td>


                            <!-- Acciones -->
                            <td>

                                <div class="table-data-feature">

                                    <?php if (empty($es_jefatura)): ?>

                                        <a href="/SGA-SEBANA/public/visit-requests/<?= $miembro['id'] ?>/reschedule"
                                           class="item"
                                           title="Reprogramar">

                                            <i class="fa-regular fa-calendar-xmark"></i>

                                        </a>


                                        <a href="/SGA-SEBANA/public/visit-requests/<?= $miembro['id'] ?>/cancel"
                                           class="item"
                                           onclick="return confirm('¿Cancelar esta solicitud?')">

                                            <i class="fa-solid fa-ban"></i>

                                        </a>

                                    <?php else: ?>

                                        <span class="text-muted small">
                                            Gestión en Admin Visitas
                                        </span>

                                    <?php endif; ?>

                                </div>

                            </td>

                        </tr>

                        <tr class="spacer"></tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>


            <!-- PAGINADOR -->
            <?php if ($totalPaginas > 1): ?>

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


<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>