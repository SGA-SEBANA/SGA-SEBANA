<?php
ob_start();
?>

<div class="row">
    <div class="col-lg-8 offset-lg-2">

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mt-4 mb-0" role="alert">
                <i class="zmdi zmdi-alert-triangle me-2"></i>
                <?php
                    if ($error === 'invalid_file') {
                        echo '<strong>Error en el archivo:</strong> El archivo adjunto no cumple con los requisitos. Debe ser PDF, JPG, JPEG o PNG y pesar un maximo de 5MB.';
                    } elseif ($error === 'db_error') {
                        echo 'Ocurrio un error al intentar guardar la solicitud. Por favor, intente de nuevo.';
                    } elseif ($error === 'monto_excedido') {
                        echo '<strong>Monto excedido:</strong> El monto maximo permitido para una ayuda economica es de 100000.';
                    } elseif ($error === 'invalid_afiliado') {
                        echo '<strong>Afiliado requerido:</strong> Debe seleccionar un afiliado valido.';
                    } else {
                        echo 'Ocurrio un error inesperado.';
                    }
                ?>
                <?php
                    $config = require BASE_PATH . '/app/config/config.php';
                    if (($config['debug'] ?? false) && !empty($_SESSION['error_detail'])) {
                        echo '<div class="small text-muted mt-2"><strong>Detalle:</strong> ' . htmlspecialchars($_SESSION['error_detail']) . '</div>';
                        unset($_SESSION['error_detail']);
                    }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm mt-4">
            <div class="card-header py-3">
                <strong class="card-title">Nueva Solicitud</strong> de Ayuda Economica
            </div>
            <div class="card-body card-block px-4 py-4">
                <form action="/SGA-SEBANA/public/ayudas/store" method="post" enctype="multipart/form-data" class="form-horizontal">

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
                            <label for="motivo" class="form-control-label font-weight-bold">Motivo</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <textarea name="motivo" id="motivo" rows="5" placeholder="Explique su situacion..." class="form-control" required></textarea>
                        </div>
                    </div>

                    <div class="row form-group mb-4">
                        <div class="col col-md-3">
                            <label for="monto_solicitado" class="form-control-label font-weight-bold">Monto Solicitado</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <i class="zmdi zmdi-money-box"></i>
                                </div>
                                <input type="number" step="0.01" min="1" max="100000" id="monto_solicitado" name="monto_solicitado" class="form-control" required>
                                <div class="input-group-addon">CRC</div>
                            </div>
                            <small class="form-text text-muted d-block mt-1">El monto maximo permitido es de 100000.</small>
                        </div>
                    </div>

                    <div class="row form-group mb-5">
                        <div class="col col-md-3">
                            <label for="evidencia" class="form-control-label font-weight-bold">Evidencia (PDF/Imagen)</label>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="custom-file-container" style="max-width: 100%; overflow: hidden;">
                                <input type="file" id="evidencia" name="evidencia" class="form-control-file text-truncate">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-4 px-0 d-flex justify-content-end">
                        <a href="/SGA-SEBANA/public/ayudas" class="btn btn-danger btn-sm mx-2 px-3 shadow-sm">
                            <i class="zmdi zmdi-close"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm px-3 shadow-sm">
                            <i class="zmdi zmdi-check"></i> Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require $_SERVER['DOCUMENT_ROOT'] . '/SGA-SEBANA/public/templates/base.html.php';
?>
