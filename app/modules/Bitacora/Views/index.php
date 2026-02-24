<?php

$title = "Bitacora";
ob_start();

?>


<div class="row">
    <div class="col-md-12">
        <!-- HEADER -->
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Bitacora</h2>

        </div>

        <!-- FILTROS -->
        <div class="card">
            <div class="card-header">
                <strong><i class="zmdi zmdi-filter-list"></i> Búsqueda y Filtros</strong>
            </div>




            <div class="card-body">

                <form method="GET">
                    <div class="row">



                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Módulo</label>
                                <select name="modulo" class="form-control">
                                    <option value="">Módulo</option>
                                    <?php foreach ($modulos as $m): ?>
                                    <option value="<?= $m['modulo'] ?>"
                                        <?= ($_GET['modulo'] ?? '') == $m['modulo'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($m['modulo']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Acción</label>
                                <select name="accion" class="form-control">
                                    <option value="">Acción</option>
                                    <?php foreach ($acciones as $a): ?>
                                    <option value="<?= $a['accion'] ?>"
                                        <?= ($_GET['accion'] ?? '') == $a['accion'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($a['accion']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Resultado</label>
                                <select name="resultado" class="form-control">
                                    <option value="">Resultado</option>
                                    <?php foreach ($resultados as $r): ?>
                                    <option value="<?= $r['resultado'] ?>"
                                        <?= ($_GET['resultado'] ?? '') == $r['resultado'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($r['resultado']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label mb-1">Fecha</label>
                                <input type="date" name="fecha" class="form-control"
                                    value="<?= $_GET['fecha'] ?? '' ?>">
                            </div>
                        </div>


                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="zmdi zmdi-search"></i>
                            </button>
                        </div>



                        <div class="col-md-1 d-flex align-items-end">
                            <a href="/SGA-SEBANA/public/bitacora/exportarExcel" class="btn btn-primary btn-block"
                                style="margin-left: 10px;"><i class="zmdi zmdi-format-list-bulleted"></i>Exportar
                            </a>
                        </div>



                    </div>
                </form>



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
                        <th>Opciones</th>


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

                        <td>
                            <a href="/SGA-SEBANA/public/bitacora/detalles/<?= $registro['id'] ?>"
                                class="au-btn au-btn-icon au-btn--blue au-btn--small ml-2" style="margin-left: 10px;">
                                <i class="zmdi zmdi-format-list-bulleted"></i> Detalles
                            </a>
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