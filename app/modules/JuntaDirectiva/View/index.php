<?php
$title = "Junta Directiva";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Junta Directiva</h2>
            <div class="d-flex">
                <a href="/SGA-SEBANA/public/junta/create" class="au-btn au-btn-icon au-btn--green au-btn--small">
                    <i class="zmdi zmdi-plus"></i> Agregar miembro
                </a>
                <a href="/SGA-SEBANA/public/junta/history" class="au-btn au-btn-icon au-btn--blue au-btn--small ml-2"
                    style="margin-left: 10px;">
                    <i class="zmdi zmdi-time"></i> Ver historial
                </a>
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Estado</th>
                        <th>Documentos</th>
                        <th>Inicio</th>
                        <th>Fin</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($junta)): ?>
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <p>No hay miembros activos en la Junta Directiva.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($junta as $miembro): ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($miembro['nombre']) ?></td>
                                <td>
                                    <span class="block-email"><?= htmlspecialchars($miembro['cargo']) ?></span>
                                </td>
                                <td>
                                    <?php if (strtolower($miembro['estado']) === 'vigente'): ?>
                                        <span class="status--process">Vigente</span>
                                    <?php else: ?>
                                        <span class="status--denied"><?= ucfirst($miembro['estado']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($miembro['total_documentos'] > 0): ?>
                                        <a href="/SGA-SEBANA/public/junta/documento/<?= $miembro['id'] ?>">
                                            <i class="zmdi zmdi-file-text"></i> Ver (<?= $miembro['total_documentos'] ?>)
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">–</span>
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
                                        <a href="/SGA-SEBANA/public/junta/finalizar/<?= $miembro['id'] ?>" class="item"
                                            data-toggle="tooltip" title="Finalizar"
                                            onclick="return confirm('¿Finalizar este miembro?')">
                                            <i class="zmdi zmdi-close-circle"></i>
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
