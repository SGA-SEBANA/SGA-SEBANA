<?php
$title = 'Solicitudes de visita';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Solicitudes de visita</h2>
            <div class="d-flex">
                <a href="/SGA-SEBANA/public/visit-requests/create"
                   class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="fa-regular fa-paper-plane"></i> Realizar solicitud
                </a>
            </div>
        </div>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                <tr>
                    <th>Codigo</th>
                    <?php if (!empty($es_jefatura)): ?>
                        <th>Afiliado</th>
                    <?php endif; ?>
                    <th>Oficina</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($solicitud)): ?>
                    <tr>
                        <td colspan="<?= !empty($es_jefatura) ? 6 : 5 ?>" class="text-center p-4">
                            No hay solicitudes registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($solicitud as $item): ?>
                        <?php
                        $estado = strtolower((string) ($item['estado'] ?? ''));
                        $allowAffiliateActions = in_array($estado, ['pendiente', 'aprobada', 'reprogramada'], true);

                        switch ($estado) {
                            case 'aprobada':
                                $class = 'status-badge status-aprobada';
                                break;
                            case 'pendiente':
                                $class = 'status-badge status-pendiente';
                                break;
                            case 'cancelada':
                            case 'rechazada':
                                $class = 'status-badge status-cancelada';
                                break;
                            default:
                                $class = 'status-badge status-desconocido';
                                break;
                        }
                        ?>
                        <tr class="tr-shadow">
                            <td><?= htmlspecialchars((string) ($item['codigo_solicitud'] ?? '-')) ?></td>

                            <?php if (!empty($es_jefatura)): ?>
                                <td><?= htmlspecialchars((string) ($item['afiliado_nombre'] ?? '')) ?></td>
                            <?php endif; ?>

                            <td><?= htmlspecialchars((string) ($item['oficina_nombre'] ?? '-')) ?></td>

                            <td>
                                <?php if (!empty($item['fecha_reprogramada'])): ?>
                                    <span style="color:#ff9800;">
                                        <?= htmlspecialchars((string) $item['fecha_reprogramada']) ?> (Reprogramada)
                                    </span>
                                <?php else: ?>
                                    <?= htmlspecialchars((string) ($item['fecha_visita'] ?? '-')) ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="<?= $class ?>">
                                    <?= htmlspecialchars(ucfirst((string) ($item['estado'] ?? '-'))) ?>
                                </span>
                            </td>

                            <td>
                                <div class="table-data-feature">
                                    <?php if (empty($es_jefatura)): ?>
                                        <?php if ($allowAffiliateActions): ?>
                                            <a href="/SGA-SEBANA/public/visit-requests/<?= (int) ($item['id'] ?? 0) ?>/reschedule"
                                               class="item" title="Reprogramar">
                                                <i class="fa-regular fa-calendar-xmark"></i>
                                            </a>
                                            <a href="/SGA-SEBANA/public/visit-requests/<?= (int) ($item['id'] ?? 0) ?>/cancel"
                                               class="item"
                                               onclick="return confirm('¿Cancelar esta solicitud?')"
                                               title="Cancelar">
                                                <i class="fa-solid fa-ban"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin acciones</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <a href="/SGA-SEBANA/public/admin/visit-requests" class="text-muted small">
                                            Gestionar en Admin Visitas
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

                        <?php
            require_once BASE_PATH . '/public/templates/components/pagination.php';
            echo render_sga_pagination((int) ($page ?? 1), (int) ($totalPaginas ?? 1), $_GET);
            ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
