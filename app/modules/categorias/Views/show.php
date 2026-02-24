<?php
/**
 * Vista de Detalles de Categoría - Auditoría HU-GC-04
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Información de la Categoría</h2>
            <a href="/SGA-SEBANA/public/Categorias" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver al listado
            </a>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <div class="mx-auto mb-3">
                            <i class="zmdi zmdi-label zmdi-hc-5x text-muted"></i>
                        </div>
                        <h3 class="mb-2"><?= htmlspecialchars($categoria['nombre']) ?></h3>
                        <span class="badge <?= $categoria['estado'] === 'activo' ? 'badge-success' : 'badge-danger' ?> text-uppercase px-3 py-2">
                            <?= $categoria['estado'] ?>
                        </span>
                        <hr>
                        <p class="text-muted small">ID del Registro: <strong>#<?= $categoria['id'] ?></strong></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <strong><i class="zmdi zmdi-info"></i> Detalles del Registro</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 font-weight-bold text-dark">Nombre oficial:</div>
                            <div class="col-sm-8 text-muted"><?= htmlspecialchars($categoria['nombre']) ?></div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-4 font-weight-bold text-dark">Tipo:</div>
                            <div class="col-sm-8 text-muted">
                                <span class="au-badge au-badge--blue">
                                    <?= htmlspecialchars($categoria['tipo'] ?? 'Sin tipo') ?>
                                </span>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-4 font-weight-bold text-dark">Fecha de creación:</div>
                            <div class="col-sm-8 text-muted">
                                <?php
                                $fecha = $categoria['fecha_creacion'] ?? null;
                                if ($fecha && strtotime($fecha)) {
                                    echo date('d/m/Y H:i:s', strtotime($fecha));
                                } else {
                                    echo 'Sin fecha';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-sm-4 font-weight-bold text-dark">Fecha de actualización:</div>
                            <div class="col-sm-8 text-muted">
                                <?php
                                $fechaAct = $categoria['fecha_actualizacion'] ?? null;
                                if ($fechaAct && strtotime($fechaAct)) {
                                    echo date('d/m/Y H:i:s', strtotime($fechaAct));
                                } else {
                                    echo 'Sin actualización';
                                }
                                ?>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-sm-12 font-weight-bold text-dark mb-2">Descripción detallada:</div>
                            <div class="col-sm-12 bg-light p-3 rounded">
                                <p class="mb-0">
                                    <?= nl2br(htmlspecialchars($categoria['descripcion'] ?: 'Esta categoría no cuenta con una descripción detallada registrada.')) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-right py-3">
                        <a href="/SGA-SEBANA/public/Categorias/<?= $categoria['id'] ?>/edit" class="btn btn-primary btn-sm px-4">
                            <i class="zmdi zmdi-edit"></i> Editar Información
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>