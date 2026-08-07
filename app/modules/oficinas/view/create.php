<?php
$title = 'Nueva Oficina';
ob_start();

$old = $old ?? [];
$office = $office ?? [];
$errors = $errors ?? [];
?>

<div class="row">
    <div class="col-lg-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva Oficina</h2>
            <a href="/SGA-SEBANA/public/oficinas" class="au-btn au-btn-icon au-btn--blue">
                <i class="zmdi zmdi-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="zmdi zmdi-alert-triangle"></i> <?= htmlspecialchars((string) $errors['general']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post" action="/SGA-SEBANA/public/oficinas/create" class="form-horizontal">
            <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>

            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-city"></i> Datos de la oficina</strong>
                </div>

                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-control-label">Codigo</label>
                            <input type="text"
                                id="codigo"
                                name="codigo"
                                class="form-control"
                                maxlength="20"
                                pattern="[A-Za-z0-9\-]+"
                                value="<?= htmlspecialchars((string) ($old['codigo'] ?? $office['codigo'] ?? '')) ?>"
                                placeholder="Ej: OF-010">
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-control-label">Nombre <span class="text-danger">*</span></label>
                            <input type="text"
                                id="nombre"
                                name="nombre"
                                class="form-control <?= !empty($errors['nombre']) ? 'is-invalid' : '' ?>"
                                maxlength="100"
                                required
                                value="<?= htmlspecialchars((string) ($old['nombre'] ?? $office['nombre'] ?? '')) ?>"
                                placeholder="Nombre de la oficina">

                            <?php if (!empty($errors['nombre'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['nombre']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="direccion" class="form-control-label">Direccion</label>
                            <input type="text"
                                id="direccion"
                                name="direccion"
                                class="form-control"
                                maxlength="150"
                                value="<?= htmlspecialchars((string) ($old['direccion'] ?? $office['direccion'] ?? '')) ?>"
                                placeholder="Direccion exacta de la oficina">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="provincia" class="form-control-label">Provincia</label>
                            <input type="text"
                                id="provincia"
                                name="provincia"
                                class="form-control"
                                maxlength="60"
                                value="<?= htmlspecialchars((string) ($old['provincia'] ?? $office['provincia'] ?? '')) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="canton" class="form-control-label">Canton</label>
                            <input type="text"
                                id="canton"
                                name="canton"
                                class="form-control"
                                maxlength="60"
                                value="<?= htmlspecialchars((string) ($old['canton'] ?? $office['canton'] ?? '')) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="distrito" class="form-control-label">Distrito</label>
                            <input type="text"
                                id="distrito"
                                name="distrito"
                                class="form-control"
                                maxlength="60"
                                value="<?= htmlspecialchars((string) ($old['distrito'] ?? $office['distrito'] ?? '')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <strong><i class="zmdi zmdi-phone"></i> Contacto y administracion</strong>
                </div>

                <div class="card-body card-block">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="telefono" class="form-control-label">Telefono</label>
                            <input type="text"
                                id="telefono"
                                name="telefono"
                                class="form-control"
                                maxlength="15"
                                pattern="[0-9+\- ]+"
                                value="<?= htmlspecialchars((string) ($old['telefono'] ?? $office['telefono'] ?? '')) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="correo" class="form-control-label">Correo</label>
                            <input type="email"
                                id="correo"
                                name="correo"
                                class="form-control <?= !empty($errors['correo']) ? 'is-invalid' : '' ?>"
                                maxlength="120"
                                value="<?= htmlspecialchars((string) ($old['correo'] ?? $office['correo'] ?? '')) ?>"
                                placeholder="oficina@sebana.cr">

                            <?php if (!empty($errors['correo'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars((string) $errors['correo']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="horario_atencion" class="form-control-label">Horario de atencion</label>
                            <input type="text"
                                id="horario_atencion"
                                name="horario_atencion"
                                class="form-control"
                                maxlength="80"
                                value="<?= htmlspecialchars((string) ($old['horario_atencion'] ?? $office['horario_atencion'] ?? '')) ?>"
                                placeholder="L-V 8:00 a.m. - 5:00 p.m.">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="responsable" class="form-control-label">Responsable</label>
                            <input type="text"
                                id="responsable"
                                name="responsable"
                                class="form-control"
                                maxlength="100"
                                value="<?= htmlspecialchars((string) ($old['responsable'] ?? $office['responsable'] ?? '')) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="coordenadas_gps" class="form-control-label">Coordenadas GPS</label>
                            <input type="text"
                                id="coordenadas_gps"
                                name="coordenadas_gps"
                                class="form-control"
                                maxlength="50"
                                value="<?= htmlspecialchars((string) ($old['coordenadas_gps'] ?? $office['coordenadas_gps'] ?? '')) ?>"
                                placeholder="9.9281,-84.0907">
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-12">
                            <label for="observaciones" class="form-control-label">Observaciones</label>
                            <textarea id="observaciones"
                                name="observaciones"
                                rows="3"
                                maxlength="500"
                                class="form-control"><?= htmlspecialchars((string) ($old['observaciones'] ?? $office['observaciones'] ?? '')) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="zmdi zmdi-save"></i> Guardar Oficina
                    </button>
                    <a href="/SGA-SEBANA/public/oficinas" class="btn btn-danger btn-sm">
                        <i class="zmdi zmdi-close"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>