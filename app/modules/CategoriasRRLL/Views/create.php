<?php
$title = "Nueva Categoría RRLL";
ob_start();
?>

<h2 class="title-1 mb-4">Nueva Categoría RRLL</h2>

<form action="/SGA-SEBANA/public/CategoriasRRLL/store" method="POST">
  <div class="form-group">
    <label for="nombre">Nombre</label>
    <input type="text" name="nombre" id="nombre" class="form-control" required>
  </div>

  <div class="form-group">
    <label for="descripcion">Descripción</label>
    <textarea name="descripcion" id="descripcion" class="form-control"></textarea>
  </div>

  <button type="submit" class="btn btn-success">Guardar</button>
  <a href="/SGA-SEBANA/public/CategoriasRRLL" class="btn btn-secondary">Cancelar</a>
</form>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>