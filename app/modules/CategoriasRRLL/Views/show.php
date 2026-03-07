<?php
$title = "Detalle Categoría RRLL";
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Detalle Categoría RRLL</h2>
            <a href="/SGA-SEBANA/public/CategoriasRRLL" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver al listado
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <strong>Información de la Categoría</strong>
            </div>
            <div class="card-body card-block">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-control-label">ID</label>
                        <p class="form-control-static"><?= htmlspecialchars($categoria['id']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-control-label">Nombre</label>
                        <p class="form-control-static"><?= htmlspecialchars($categoria['nombre']) ?></p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-control-label">Descripción</label>
                        <p class="form-control-static"><?= htmlspecialchars($categoria['descripcion']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-control-label">Estado</label>
                        <p class="form-control-static">
                            <?= $categoria['estado'] === 'activo' ? 'Activo' : 'Inactivo' ?>
                        </p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-control-label">Fecha de Creación</label>
                        <p class="form-control-static"><?= htmlspecialchars($categoria['fecha_creacion'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-control-label">Última Actualización</label>
                        <p class="form-control-static"><?= htmlspecialchars($categoria['fecha_actualizacion'] ?? '') ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $categoria['id'] ?>/edit" class="btn btn-primary btn-sm">
                    <i class="zmdi zmdi-edit"></i> Editar
                </a>
                <a href="/SGA-SEBANA/public/CategoriasRRLL" class="btn btn-secondary btn-sm">
                    <i class="zmdi zmdi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>