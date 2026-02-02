<?php
$title = "Junta Directiva";
ob_start();
?>

<h1 class="mb-4">Historial Junta Directiva</h1>


<div class="table-responsive table-responsive-data2">
    <div class="mb-3">
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
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
        <?php foreach ($historial as $miembro): ?>
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

                    <a href="/SGA-SEBANA/public/junta/activar/<?= $miembro['id'] ?>"
                     class="item"
                     title="Re-activar miembro"
                      onclick="return confirm('¿activar este miembro?')">
                   <i class="zmdi zmdi-check-circle"></i>
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
