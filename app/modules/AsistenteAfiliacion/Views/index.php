<?php
ob_start();
?>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Solicitudes de Afiliacion</h2>
            <a href="/SGA-SEBANA/public/afiliarse" class="btn btn-outline-primary btn-sm">
                <i class="zmdi zmdi-open-in-new me-1"></i>Formulario Publico
            </a>
        </div>

        <?php if (($success ?? null) === 'estado'): ?>
            <div class="alert alert-success">Estado actualizado correctamente.</div>
        <?php endif; ?>

        <?php if (($error ?? null) === 'not_found'): ?>
            <div class="alert alert-danger">La solicitud indicada no existe.</div>
        <?php endif; ?>

        <div class="table-responsive table-responsive-data2 shadow-sm rounded border">
            <table class="table table-data2 bg-white mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ID</th>
                        <th>Cedula</th>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th>Envio</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($solicitudes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No hay solicitudes registradas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($solicitudes as $s): ?>
                        <?php
                            $estado = strtolower(trim((string) ($s['estado'] ?? '')));
                            $badge = 'badge bg-secondary';
                            if ($estado === 'enviada_aprobacion') {
                                $badge = 'badge bg-warning text-dark';
                            } elseif ($estado === 'aprobada') {
                                $badge = 'badge bg-success';
                            } elseif ($estado === 'rechazada') {
                                $badge = 'badge bg-danger';
                            } elseif ($estado === 'pdf_generado') {
                                $badge = 'badge bg-info text-dark';
                            }
                        ?>
                        <tr class="tr-shadow">
                            <td><strong>#<?= htmlspecialchars((string) ($s['id'] ?? '')) ?></strong></td>

                            <td><?= htmlspecialchars((string) ($s['cedula'] ?? '')) ?></td>
                            <td><?= htmlspecialchars(trim((string) (($s['nombre'] ?? '') . ' ' . ($s['apellido1'] ?? '')))) ?></td>
                            <td><?= htmlspecialchars((string) ($s['tipo_usuario'] ?? '')) ?></td>
                            <td><span class="<?= $badge ?>"><?= htmlspecialchars((string) ($s['estado'] ?? '')) ?></span></td>
                            <td><?= !empty($s['fecha_envio']) ? date('d/m/Y H:i', strtotime((string) $s['fecha_envio'])) : '-' ?></td>
                            <td>
                                <div class="table-data-feature justify-content-end">
                                    <a href="/SGA-SEBANA/public/asistente-afiliacion/solicitudes/<?= urlencode((string) ($s['id'] ?? '')) ?>" class="item btn btn-outline-info btn-sm" title="Ver detalle">
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
require BASE_PATH . '/public/templates/base.html.php';
?>
