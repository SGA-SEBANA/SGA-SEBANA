<?php
$title = "Junta Directiva";

ob_start();
?>

<h1>Junta Directiva</h1>

<table class="table table-bordered">

    <thead>
        <tr>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Estado</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Finalizacion</th>

        </tr>

    </thead>

    <tbody>
        <?php foreach ($junta as $miembro): ?>
            <tr>
                <td><?= $miembro['nombre'] ?></td>
                <td><?= $miembro['cargo'] ?></td>
                <td><?= $miembro['estado'] ?></td>
                <td><?= $miembro['fecha_inicio'] ?></td>
                <td><?= $miembro['fecha_fin'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>


</table>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
