<?php
$title = "Bitacora";
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Bitacora</h2>
            <div class="d-flex">
                
            </div>
        </div>

        <!-- TABLA -->
        <div class="table-responsive table-responsive-data2">
            <table class="table table-data2">
                <thead>
                    <tr>
                        <th>Accion</th>
                        <th>Modulo</th>
                        <th>Entidad</th>
                        <th>Descripcion</th>
                  
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bitacora)): ?>
                        <tr>
                            <td colspan="8" class="text-center p-4">
                                <p>No hay miembros activos en la Junta Directiva.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bitacora as $registro): ?>
                            <tr class="tr-shadow">
                                <td><?= htmlspecialchars($registro['accion']) ?></td>


                                <td><?= htmlspecialchars($registro['modulo']) ?></td>
                              
                            
                                <td><?= htmlspecialchars($registro['entidad']) ?></td>

                                <td><?= htmlspecialchars($registro['descripcion']) ?></td>

                        
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
