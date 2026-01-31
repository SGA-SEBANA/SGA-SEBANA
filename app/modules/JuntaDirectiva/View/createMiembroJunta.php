<?php
$title = "Agregar miembro de Junta Directiva";
ob_start();
?>

<h1 class="mb-4">Agregar miembro de Junta Directiva</h1>

<form method="POST">

    <div class="form-group mb-3">
        <label>Afiliado</label>
        <select name="afiliado_id" class="form-control" required>
            <option value="">Seleccione un afiliado</option>
            <?php foreach ($afiliados as $a): ?>
              <option value="<?= $a['id'] ?>">
                <?= $a['nombre_completo'] ?> - <?= $a['cedula'] ?>
              </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group mb-3">
        <label>Cargo</label>
        <input type="text" name="cargo" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Estado</label>
        <input type="text" name="estado" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Fecha inicio</label>
        <input type="date" name="fecha_inicio" class="form-control" required>
    </div>

    <div class="form-group mb-3">
        <label>Fecha fin</label>
        <input type="date" name="fecha_fin" class="form-control">
    </div>

    <div class="form-group mb-3">
        <label>Periodo</label>
        <input type="text" name="periodo" class="form-control">
    </div>

    <div class="form-group mb-3">
        <label>Responsabilidades</label>
        <textarea name="responsabilidades" class="form-control"></textarea>
    </div>

    <div class="form-group mb-3">
        <label>Documentos</label>
        <textarea name="documentos" class="form-control"></textarea>
    </div>

    <div class="form-group mb-3">
        <label>Observaciones</label>
        <textarea name="observaciones" class="form-control"></textarea>
    </div>

    <button type="submit" class="btn btn-success">
        <i class="zmdi zmdi-save"></i> Guardar
    </button>

    <a href="/SGA-SEBANA/public/junta" class="btn btn-secondary">
        Cancelar
    </a>

</form>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
