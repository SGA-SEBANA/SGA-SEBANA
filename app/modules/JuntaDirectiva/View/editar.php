<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">
                Editar Miembro Junta Directiva
            </h2>
            <a href="/SGA-SEBANA/public/junta" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

   

        <form action="/SGA-SEBANA/public/junta/edit/<?= $miembro['id'] ?>" method="post"
              class="form-horizontal">
              

            <!-- DATOS DEL CARGO -->
            <div class="card">
                <div class="card-header">
                   <strong><?= htmlspecialchars($miembro['nombre']) ?></strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-control-label">Cargo</label>
                            <input type="text" name="cargo" class="form-control"
                                   value="<?= htmlspecialchars($miembro['cargo']) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-control-label">Estado</label>
                            <select name="estado" class="form-control">
                                <option value="vigente" <?= $miembro['estado']=='vigente'?'selected':'' ?>>Vigente</option>
                                <option value="finalizado" <?= $miembro['estado']=='finalizado'?'selected':'' ?>>Finalizado</option>
                                <option value="suspendido" <?= $miembro['estado']=='suspendido'?'selected':'' ?>>Suspendido</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-control-label">Fecha Inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control"
                                   value="<?= $miembro['fecha_inicio'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-control-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" class="form-control"
                                   value="<?= $miembro['fecha_fin'] ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-control-label">Periodo</label>
                            <input type="text" name="periodo" class="form-control"
                                   value="<?= htmlspecialchars($miembro['periodo']) ?>">
                        </div>
                    </div>

                </div>
            </div>

            <!-- RESPONSABILIDADES -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-assignment"></i> Responsabilidades</strong>
                </div>
                <div class="card-body card-block">
                    <textarea name="responsabilidades" rows="3"
                              class="form-control"><?= htmlspecialchars($miembro['responsabilidades']) ?></textarea>
                </div>
            </div>

            <!-- DOCUMENTOS Y OBSERVACIONES -->
            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-file"></i> Documentos y Observaciones</strong>
                </div>
                <div class="card-body card-block">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-control-label">Documentos</label>
                            <input type="text" name="documentos" class="form-control"
                                   value="<?= htmlspecialchars($miembro['documentos']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-control-label">Observaciones</label>
                            <input type="text" name="observaciones" class="form-control"
                                   value="<?= htmlspecialchars($miembro['observaciones']) ?>">
                        </div>
                    </div>

                </div>
                <div class="card-footer">
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
