<?php
$title = "Categorías RRLL";
ob_start();
?>

<div class="row">
  <div class="col-md-12">
    <h2 class="title-1 mb-4">Gestión de Categorías RRLL</h2>

    <!-- ALERTAS -->
    <?php if (!empty($_GET['success'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="zmdi zmdi-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <?php if (!empty($_GET['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="zmdi zmdi-alert-triangle"></i> <?= htmlspecialchars($_GET['error']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <!-- FILTROS -->
    <div class="card">
      <div class="card-header">
        <strong><i class="zmdi zmdi-filter-list"></i> Filtros de Categorías RRLL</strong>
      </div>
      <div class="card-body">
        <form action="/SGA-SEBANA/public/CategoriasRRLL" method="GET">
          <div class="row">
            <div class="col-md-4">
              <label for="q">Filtrar por nombre:</label>
              <input type="text" name="q" id="q" class="form-control"
                     value="<?= htmlspecialchars($filtros['q'] ?? '') ?>" placeholder="Ingrese nombre">
            </div>
            <div class="col-md-3">
              <label for="fecha">Fecha actualización:</label>
              <input type="date" name="fecha" id="fecha" class="form-control"
                     value="<?= htmlspecialchars($filtros['fecha'] ?? '') ?>">
            </div>
            <div class="col-md-5 text-right align-self-end mt-3">
              <button type="submit" class="au-btn au-btn-icon au-btn--blue au-btn--small">
                <i class="zmdi zmdi-search"></i> Buscar
              </button>
              <a href="/SGA-SEBANA/public/CategoriasRRLL/create"
                 class="au-btn au-btn-icon au-btn--green au-btn--small" style="margin-left: 8px;">
                <i class="zmdi zmdi-plus"></i> Nueva Categoría
              </a>
     <a href="/SGA-SEBANA/public/CategoriasRRLL/exportarHistorialPDF"
   class="au-btn au-btn-icon au-btn--green au-btn--small" style="margin-left: 8px;">
   <i class="zmdi zmdi-download"></i> Exportar Historial PDF
</a>


            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- TABLA -->
    <div class="table-responsive table-responsive-data2 mt-4">
      <table class="table table-data2">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Fecha Actualización</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categorias)): ?>
            <tr>
              <td colspan="6" class="text-center p-4">
                No se encontraron categorías RRLL con los filtros aplicados.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($categorias as $cat): ?>
              <tr class="tr-shadow">
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['nombre']) ?></td>
                <td><?= htmlspecialchars($cat['descripcion'] ?: 'Sin descripción') ?></td>
                <td>
                  <?php if ($cat['estado'] === 'activo'): ?>
                    <span class="status--process">Activo</span>
                  <?php else: ?>
                    <span class="status--denied">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td><?= !empty($cat['fecha_actualizacion']) ? htmlspecialchars($cat['fecha_actualizacion']) : '-' ?></td>
                <td>
                  <div class="table-data-feature">
                    <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/edit" class="item" title="Editar">
                      <i class="zmdi zmdi-edit"></i>
                    </a>
                    <form action="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/toggleEstado" method="POST" style="display:inline;">
                      <?php if ($cat['estado'] === 'activo'): ?>
                        <button type="submit" class="item" title="Inactivar"
                                onclick="return confirm('¿Inactivar categoría RRLL?');">
                          <i class="zmdi zmdi-block"></i>
                        </button>
                      <?php else: ?>
                        <button type="submit" class="item" title="Activar"
                                onclick="return confirm('¿Activar categoría RRLL?');">
                          <i class="zmdi zmdi-check"></i>
                        </button>
                      <?php endif; ?>
                    </form>
                    <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/show" class="item" title="Ver Detalles">
                      <i class="zmdi zmdi-eye"></i>
                    </a>
                  </div>
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
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>