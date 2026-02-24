<?php
$title = "Detalle Bitácora";
ob_start();
?>

<div class="row">
    <div class="col-md-12">

        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalle de Bitácora</h2>

        </div>

        <div class="overview-wrap mb-4">

            <a href="/SGA-SEBANA/public/bitacora" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la bitacora
            </a>
        </div>




        <?php if (!$registro): ?>
        <div class="alert alert-warning">
            No se encontró el registro solicitado.
        </div>
        <?php else: ?>

        <div class="card">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>ID</th>
                        <td><?= htmlspecialchars($registro['id']) ?></td>
                    </tr>
                    <tr>
                        <th>Acción</th>
                        <td><?= htmlspecialchars($registro['accion']) ?></td>
                    </tr>
                    <tr>
                        <th>Módulo</th>
                        <td><?= htmlspecialchars($registro['modulo']) ?></td>
                    </tr>
                    <tr>
                        <th>Entidad</th>
                        <td><?= htmlspecialchars($registro['entidad']) ?></td>
                    </tr>
                    <tr>
                        <th>Descripción</th>
                        <td><?= htmlspecialchars($registro['descripcion']) ?></td>
                    </tr>
                    <tr>
                        <th>Datos Anteriores</th>
                        <td><?= htmlspecialchars($registro['datos_anteriores'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Datos Nuevos</th>
                        <td><?= htmlspecialchars($registro['datos_nuevos'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>IP</th>
                        <td><?= htmlspecialchars($registro['ip_address']) ?></td>
                    </tr>
                    <tr>
                        <th>Resultado</th>
                        <td><?= htmlspecialchars($registro['resultado']) ?></td>
                    </tr>
                    <tr>
                        <th>Código Error</th>
                        <td><?= htmlspecialchars($registro['codigo_error'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <th>Fecha</th>
                        <td><?= htmlspecialchars($registro['fecha_creacion']) ?></td>
                    </tr>
                    <tr>
                        <th>Mensaje</th>
                        <td><?= htmlspecialchars($registro['mensaje_error'] ?? '') ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';