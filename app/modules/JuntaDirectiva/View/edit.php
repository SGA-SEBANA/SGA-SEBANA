<?php
/**
 * Vista de Edición de Miembro Junta
 */
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Editar Miembro Junta</h2>
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <form action="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?? '' ?>" method="post"
            enctype="multipart/form-data" class="form-horizontal">

            <div class="card">
                <div class="card-header">
                    <strong><?= htmlspecialchars($miembro['nombre'] ?? 'Miembro') ?></strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cargo" class="form-control-label">Cargo</label>
                            <input type="text" id="cargo" name="cargo"
                                value="<?= htmlspecialchars($miembro['cargo'] ?? '') ?>" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="estado" class="form-control-label">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="vigente" <?= (strtolower($miembro['estado'] ?? '') == 'vigente') ? 'selected' : '' ?>>Vigente</option>
                                <option value="finalizado" <?= (strtolower($miembro['estado'] ?? '') == 'finalizado') ? 'selected' : '' ?>>Finalizado</option>
                                <option value="suspendido" <?= (strtolower($miembro['estado'] ?? '') == 'suspendido') ? 'selected' : '' ?>>Suspendido</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="fecha_inicio" class="form-control-label">Fecha Inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio"
                                value="<?= $miembro['fecha_inicio'] ?? '' ?>" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label for="fecha_fin" class="form-control-label">Fecha Fin</label>
                            <input type="date" id="fecha_fin" name="fecha_fin"
                                value="<?= $miembro['fecha_fin'] ?? '' ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="periodo" class="form-control-label">Periodo</label>
                            <input type="text" id="periodo" name="periodo"
                                value="<?= htmlspecialchars($miembro['periodo'] ?? '') ?>" class="form-control">
                        </div>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment"></i> Responsabilidades y Documentos</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="responsabilidades" class="form-control-label">Responsabilidades</label>
                            <textarea name="responsabilidades" id="responsabilidades" rows="3"
                                class="form-control"><?= htmlspecialchars($miembro['responsabilidades'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-control-label">Documentos Actuales</label>
                            <?php if (!empty($documentos)): ?>
                                <ul class="list-group mb-3">
                                    <?php foreach ($documentos as $doc): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="/SGA-SEBANA/public/junta/ver-documento/<?= $doc['id'] ?? '' ?>"
                                                target="_blank">
                                                <i class="zmdi zmdi-file"></i>
                                                <?= htmlspecialchars($doc['nombre_original'] ?? 'Documento') ?>
                                            </a>
                                            <a href="/SGA-SEBANA/public/junta/eliminar-documento/<?= $doc['id'] ?? '' ?>"
                                                onclick="return confirm('¿Eliminar este documento?')"
                                                class="btn btn-danger btn-sm">
                                                <i class="zmdi zmdi-delete"></i>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">Sin documentos adjuntos.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="documentos" class="form-control-label">Agregar más documentos</label>
                            <input type="file" name="documentos[]" multiple class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label for="observaciones" class="form-control-label">Observaciones</label>
                            <input type="text" name="observaciones" id="observaciones"
                                value="<?= htmlspecialchars($miembro['observaciones'] ?? '') ?>" class="form-control">
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>