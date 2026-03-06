<?php
$title = "Categorías RRLL";
ob_start();
?>

<h2 class="title-1 mb-4">Gestión de Categorías RRLL</h2>

<div class="card">
  <div class="card-header">
    <strong><i class="zmdi zmdi-filter-list"></i> Filtros</strong>
  </div>
  <div class="card-body">
    <form method="GET" action="/SGA-SEBANA/public/CategoriasRRLL">
      <input type="text" name="q" placeholder="Buscar..." value="<?= htmlspecialchars($filtros['q'] ?? '') ?>">
      <input type="date" name="fecha" value="<?= htmlspecialchars($filtros['fecha'] ?? '') ?>">
      <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
  </div>
</div>

<div class="table-responsive">
  <table class="table table-data2">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categorias as $cat): ?>
        <tr>
          <td><?= $cat['id'] ?></td>
          <td><?= htmlspecialchars($cat['nombre']) ?></td>
          <td><?= htmlspecialchars($cat['descripcion']) ?></td>
          <td><?= htmlspecialchars($cat['estado']) ?></td>
          <td>
            <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/edit">Editar</a>
            <form action="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/delete" method="POST" style="display:inline;">
              <button type="submit">Eliminar</button>
            </form>
            <a href="/SGA-SEBANA/public/CategoriasRRLL/<?= $cat['id'] ?>/show">Ver</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';