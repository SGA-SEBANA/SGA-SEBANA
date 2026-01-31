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
</div>

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
        <?php foreach ($junta as $miembro): ?>
            <tr class="tr-shadow">

                <td><?= $miembro['nombre'] ?></td>

                <td><?= $miembro['cargo'] ?></td>

                <td>
                    <?php if ($miembro['estado'] === 'vigente'): ?>
                        <span class="status--process">Vigente</span>
                    <?php else: ?>
                        <span class="status--denied"><?= ucfirst($miembro['estado']) ?></span>
                    <?php endif; ?>
                </td>

                <td><?= $miembro['fecha_inicio'] ?></td>
                <td><?= $miembro['fecha_fin'] ?? '—' ?></td>

                <td>
                    <div class="table-data-feature">
                
                       <a href="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?>" class="item">
                       <i class="zmdi zmdi-edit"></i>
                       </a>

                        <a href="<?= $miembro['afiliado_id'] ?>" 
                           class="item" data-bs-toggle="tooltip" title="Eliminar"
                           onclick="return confirm('¿Eliminar miembro?')">
                            <i class="zmdi zmdi-delete"></i>
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
