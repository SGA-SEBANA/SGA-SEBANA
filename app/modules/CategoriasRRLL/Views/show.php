<?php
$title = "Detalle Categoría RRLL";
ob_start();
?>

<div class="row">
  <div class="col-md-12">
    <h2 class="title-1 mb-4">Detalle de Categoría RRLL</h2>

    <div class="card">
      <div class="card-header">
        <strong><i class="zmdi zmdi-eye"></i> Información de la Categoría</strong>
      </div>
      <div class="card-body">
        <dl class="row">
          <dt class="col-sm-3">ID</dt>
          <dd class="col-sm-9"><?= htmlspecialchars($categoria['id']) ?></dd>

          <dt class="col-sm-3">Nombre</dt>
          <dd class="col-sm-9"><?= htmlspecialchars($categoria['nombre']) ?></dd>

          <dt class="col-sm-3">Descripción</dt>
          <dd class="col-sm-9"><?= htmlspecialchars($categoria['descripcion'] ?: 'Sin descripción') ?></dd>

          <dt class="col-sm-3">Estado</dt>
          <dd class="col-sm-9">
            <?php if ($categoria['estado'] === 'activo'): ?>
              <span class="status--process">Activo</span>
            <?php else: ?>
              <span class="status--denied">Inactivo</span>
            <?php endif; ?>
          </dd>

          <dt class="col-sm-3">Fecha actualización</dt>
          <dd class="col-sm-9"><?= htmlspecialchars($categoria['fecha_actualizacion'] ?? '-') ?></dd>
        </dl>
      </div>
      <div class="card-footer text-right">
        <a href="/SGA-SEBANA/public/CategoriasRRLL" class="btn btn-secondary">
          <i class="zmdi zmdi-arrow-left"></i> Volver al listado
        </a>
        <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $categoria['id'] ?>/edit" class="btn btn-warning">
          <i class="zmdi zmdi-edit"></i> Editar
        </a>
      </div>
    </div>
  </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>