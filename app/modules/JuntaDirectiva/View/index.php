<?php
$title = "Junta Directiva";
ob_start();
?>

<h1 class="mb-4">Junta Directiva</h1>

<div class="table-responsive table-responsive-data2">
    <div class="mb-3">
        <a href="/SGA-SEBANA/public/junta/create" class="btn btn-success">
            <i class="zmdi zmdi-plus"></i> Agregar miembro
        </a>

        <a href="/SGA-SEBANA/public/junta/history" class="btn btn-info">
            <i class="zmdi zmdi-time"></i> Ver historial
        </a>
    </div>

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
        <?php foreach ($junta as $miembro): ?>
            <tr class="tr-shadow">

                <td><?= htmlspecialchars($miembro['nombre']) ?></td>

                <td><?= htmlspecialchars($miembro['cargo']) ?></td>

                <td>
                    <?php if (strtolower($miembro['estado']) === 'vigente'): ?>
                        <span class="status--process">Vigente</span>
                    <?php else: ?>
                        <span class="status--denied">
                            <?= htmlspecialchars($miembro['estado']) ?>
                        </span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if($miembro['total_documentos'] > 0): ?>
                        <a href="/SGA-SEBANA/public/junta/documento/<?= $miembro['id'] ?>">
                            Ver documentos (<?= $miembro['total_documentos'] ?>)
                        </a>
                    <?php else: ?>
                        Sin documentos
                    <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($miembro['fecha_inicio']) ?></td>
                <td><?= $miembro['fecha_fin'] ? htmlspecialchars($miembro['fecha_fin']) : '—' ?></td>

                <td>
                    <div class="table-data-feature">
                        <a href="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?>" class="item">
                            <i class="zmdi zmdi-edit"></i>
                        </a>

                        <a href="/SGA-SEBANA/public/junta/finalizar/<?= $miembro['id'] ?>"
                           class="item"
                           title="Finalizar miembro"
                           onclick="return confirm('¿Finalizar este miembro?')">
                            <i class="zmdi zmdi-close-circle"></i>
                        </a>
                    </div>
                </td>

            </tr>
            <tr class="spacer"></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
