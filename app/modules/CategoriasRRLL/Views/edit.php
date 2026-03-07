<?php
$title = "Editar Categoría RRLL";
ob_start();
?>

<h2 class="title-1 mb-4">Editar Categoría RRLL</h2>

<form action="/SGA-SEBANA/public/CategoriasRRLL/<?= $categoria['id'] ?>/update" method="POST">
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control"
           value="<?= htmlspecialchars($categoria['nombre']) ?>" required>
  </div>

  <div class="form-group">
    <label for="descripcion">Descripción</label>
    <textarea name="descripcion" id="descripcion" class="form-control"><?= htmlspecialchars($categoria['descripcion']) ?></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Actualizar</button>
  <a href="/SGA-SEBANA/public/CategoriasRRLL" class="btn btn-secondary">Cancelar</a>
</form>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>