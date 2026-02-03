<?php
$title = "Historial Junta Directiva";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Historial Junta Directiva</h2>
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historial)): ?>
                        <tr>
                            <td colspan="6" class="text-center p-4">
                                <p>No hay historial disponible.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historial as $miembro): ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($miembro['nombre']) ?></td>
                                <td><span class="block-email"><?= htmlspecialchars($miembro['cargo']) ?></span></td>
                                <td>
                                    <?php if (strtolower($miembro['estado']) === 'vigente'): ?>
                                        <span class="status--process">Vigente</span>
                                    <?php else: ?>
                                        <span class="status--denied"><?= ucfirst($miembro['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($miembro['fecha_inicio']) ?></td>
                                <td><?= $miembro['fecha_fin'] ? htmlspecialchars($miembro['fecha_fin']) : '—' ?></td>
                                <td>
                                    <div class="table-data-feature">
                                        <a href="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?>" class="item"
                                            data-toggle="tooltip" title="Editar">
                                            <i class="zmdi zmdi-edit"></i>
                                        </a>
                                        <a href="/SGA-SEBANA/public/junta/activar/<?= $miembro['id'] ?>" class="item"
                                            data-toggle="tooltip" title="Re-activar"
                                            onclick="return confirm('¿Re-activar este miembro?')">
                                            <i class="zmdi zmdi-check-circle"></i>
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
