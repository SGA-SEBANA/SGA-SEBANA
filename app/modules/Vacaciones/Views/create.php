<?php
ob_start();
?>

<div class="row mt-3">
    <div class="col-lg-8 offset-lg-2">

        <div class="overview-wrap mb-4 px-2">
            <h2 class="title-1">Solicitar Vacaciones</h2>
            <a href="/SGA-SEBANA/public/vacaciones" class="btn btn-secondary shadow-sm">
                <i class="zmdi zmdi-arrow-left me-2"></i>Volver al Listado
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mt-4 mb-4" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i>
                <?php
                    if ($error === 'db_error') {
                        echo '<strong>Error:</strong> Ocurrio un problema al guardar la solicitud.';
                    } elseif ($error === 'invalid_dates') {
                        echo '<strong>Error:</strong> Verifique las fechas ingresadas.';
                    } elseif ($error === 'invalid_afiliado') {
                        echo '<strong>Error:</strong> Debe seleccionar un afiliado valido.';
                    } else {
                        echo 'Ocurrio un error inesperado.';
                    }
                ?>
                <?php if (!empty($error_detail)): ?>
                    <div class="mt-2"><small>Detalle: <?= htmlspecialchars($error_detail) ?></small></div>
                <?php endif; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mt-2 border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <strong class="card-title text-primary"><i class="fa-solid fa-umbrella-beach me-2"></i>Nueva Solicitud</strong>
            </div>
            <div class="card-body card-block px-5 py-4">
                <form action="/SGA-SEBANA/public/vacaciones/store" method="post" class="form-horizontal">
                    <?php if (!empty($es_jefatura)): ?>
                        <div class="row form-group mb-4">
                            <div class="col col-md-3">
                                <label for="afiliado_id" class="form-control-label font-weight-bold">Afiliado</label>
                            </div>
                            <div class="col-12 col-md-9">
                                <select id="afiliado_id" name="afiliado_id" class="form-control" required>
                                    <option value="">Seleccione un afiliado...</option>
                                    <?php foreach (($afiliados ?? []) as $afiliado): ?>
                                        <option value="<?= (int) ($afiliado['id'] ?? 0) ?>">
                                            <?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>
                                            (<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row form-group mb-4">
                        <div class="col col-md-3">
                            <label for="fecha_inicio" class="form-control-label font-weight-bold">Fecha de Inicio</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" required>
                            <small class="form-text text-muted">Primer dia de sus vacaciones.</small>
                        </div>
                    </div>

                    <div class="row form-group mb-4">
                        <div class="col col-md-3">
                            <label for="fecha_fin" class="form-control-label font-weight-bold">Fecha de Fin</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" required>
                            <small class="form-text text-muted">Ultimo dia de sus vacaciones.</small>
                        </div>
                    </div>

                    <div class="row form-group mb-4">
                        <div class="col col-md-3">
                            <label for="motivo" class="form-control-label font-weight-bold">Motivo / Comentarios</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <textarea name="motivo" id="motivo" rows="4" placeholder="Opcional: Indique el motivo o agregue un comentario para su jefatura..." class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-4 px-0 d-flex justify-content-end">
                        <a href="/SGA-SEBANA/public/vacaciones" class="btn btn-outline-danger btn-sm mx-2 px-4 shadow-sm">
                            <i class="zmdi zmdi-close me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm">
                            <i class="zmdi zmdi-mail-send me-1"></i> Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        document.getElementById('fecha_fin').min = this.value;
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>
