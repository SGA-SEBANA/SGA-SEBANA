<?php
$title = $action === 'create' ? 'Nueva Oficina' : 'Editar Oficina';
ob_start();
$old = $old ?? [];
$office = $office ?? [];
?>

<form method="post" action="<?= $action === 'create' ? '/oficinas' : '/oficinas/' . $office['id'] ?>">
    <div class="mb-3">
        <label>Código</label>
        <input type="text" name="codigo" class="form-control"
            value="<?= htmlspecialchars($old['codigo'] ?? $office['codigo'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control"
            value="<?= htmlspecialchars($old['nombre'] ?? $office['nombre'] ?? '') ?>">
        <?php if(!empty($errors['nombre'])): ?><small
            class="text-danger"><?= $errors['nombre'] ?></small><?php endif; ?>
    </div>
    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control"
            value="<?= htmlspecialchars($old['direccion'] ?? $office['direccion'] ?? '') ?>">
        <?php if(!empty($errors['direccion'])): ?><small
            class="text-danger"><?= $errors['direccion'] ?></small><?php endif; ?>
    </div>
    <div class="mb-3">
        <label>Provincia</label>
        <input type="text" name="provincia" class="form-control"
            value="<?= htmlspecialchars($old['provincia'] ?? $office['provincia'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Cantón</label>
        <input type="text" name="canton" class="form-control"
            value="<?= htmlspecialchars($old['canton'] ?? $office['canton'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Distrito</label>
        <input type="text" name="distrito" class="form-control"
            value="<?= htmlspecialchars($old['distrito'] ?? $office['distrito'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" class="form-control"
            value="<?= htmlspecialchars($old['telefono'] ?? $office['telefono'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Correo</label>
        <input type="email" name="correo" class="form-control"
            value="<?= htmlspecialchars($old['correo'] ?? $office['correo'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Horario Atención</label>
        <input type="text" name="horario_atencion" class="form-control"
            value="<?= htmlspecialchars($old['horario_atencion'] ?? $office['horario_atencion'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Responsable</label>
        <input type="text" name="responsable" class="form-control"
            value="<?= htmlspecialchars($old['responsable'] ?? $office['responsable'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Coordenadas GPS</label>
        <input type="text" name="coordenadas_gps" class="form-control"
            value="<?= htmlspecialchars($old['coordenadas_gps'] ?? $office['coordenadas_gps'] ?? '') ?>">
    </div>
    <div class="mb-3">
        <label>Observaciones</label>
        <textarea name="observaciones"
            class="form-control"><?= htmlspecialchars($old['observaciones'] ?? $office['observaciones'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><?= $action === 'create' ? 'Crear' : 'Actualizar' ?></button>
</form>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>