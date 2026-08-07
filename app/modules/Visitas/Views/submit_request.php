<?php
$title = 'Nueva solicitud de visita';
ob_start();
?>

<div class="row">
    <div class="col-md-12">
        <div class="overview-wrap mb-4">
            <h2 class="title-1">Nueva solicitud de visita</h2>
            <a href="/SGA-SEBANA/public/visit-requests" class="au-btn au-btn-icon au-btn--blue">
                <i class="fa-solid fa-arrow-left"></i> Volver
            </a>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger mb-3">
                <?= htmlspecialchars((string) $error) ?>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">
                <strong>Datos de la solicitud</strong>
            </div>

            <div class="card-body">
                <form method="POST" action="/SGA-SEBANA/public/visit-requests/create">

                    <!-- CSRF -->
                    <?= \App\Modules\Usuarios\Helpers\SecurityHelper::csrfField() ?>

                    <div class="row">

                        <?php if (!empty($es_jefatura)): ?>
                            <div class="col-md-7 mb-3">
                                <label for="buscarCedula">Buscar afiliado por cédula</label>
                                <input type="text" id="buscarCedula" class="form-control"
                                       placeholder="Digite la cédula del afiliado" maxlength="9">
                                <small class="form-text text-muted">
                                    Solo números, sin guiones ni espacios.
                                </small>
                            </div>

                            <div class="col-md-5 mb-3">
                                <label for="afiliadoSelect">Afiliado</label>
                                <select name="afiliado_id" id="afiliadoSelect" class="form-control" required>
                                    <option value="">-- Seleccione un afiliado --</option>

                                    <?php foreach (($afiliados ?? []) as $afiliado): ?>
                                        <?php if (empty($afiliado['id'])) continue; ?>

                                        <option value="<?= (int) $afiliado['id'] ?>"
                                                data-cedula="<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>"
                                                data-nombre="<?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>">
                                            <?= htmlspecialchars((string) ($afiliado['nombre_completo'] ?? '')) ?>
                                            (<?= htmlspecialchars((string) ($afiliado['cedula'] ?? '')) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        <?php else: ?>
                            <div class="col-md-12 mb-3">
                                <div class="alert alert-info mb-0">
                                    La solicitud se registrará con el afiliado logueado:
                                    <strong><?= htmlspecialchars((string) ($afiliadoData['nombre_completo'] ?? '')) ?></strong>
                                    (<?= htmlspecialchars((string) ($afiliadoData['cedula'] ?? '')) ?>)
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-4 mb-3">
                            <label for="oficina_id">Oficina</label>
                            <select name="oficina_id" id="oficina_id" class="form-control" required>
                                <option value="">-- Seleccione una oficina --</option>
                                <?php foreach (($oficinas ?? []) as $oficina): ?>
                                    <?php if (empty($oficina['id'])) continue; ?>

                                    <option value="<?= (int) $oficina['id'] ?>">
                                        <?= htmlspecialchars((string) ($oficina['nombre'] ?? '')) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="numeroEmpleado">Número de empleado / cédula</label>
                            <input type="text"
                                   id="numeroEmpleado"
                                   maxlength="20"
                                   name="numero_empleado"
                                   value="<?= !empty($es_jefatura)
                                        ? ''
                                        : htmlspecialchars((string) ($afiliadoData['cedula'] ?? '')) ?>"
                                   class="form-control"
                                   <?= empty($es_jefatura) ? 'readonly' : '' ?>
                                   required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="nombreEmpleado">Nombre del empleado</label>
                            <input type="text"
                                   id="nombreEmpleado"
                                   maxlength="50"
                                   name="nombre_empleado"
                                   value="<?= !empty($es_jefatura)
                                        ? ''
                                        : htmlspecialchars((string) ($afiliadoData['nombre_completo'] ?? '')) ?>"
                                   class="form-control"
                                   <?= empty($es_jefatura) ? 'readonly' : '' ?>
                                   required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="fecha_visita">Fecha de visita</label>
                            <input type="date" name="fecha_visita" id="fecha_visita" class="form-control" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="hora_visita">Hora de visita</label>
                            <input type="time" name="hora_visita" id="hora_visita" class="form-control">
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="tipo_visita">Tipo de visita</label>
                            <select name="tipo_visita" id="tipo_visita" class="form-control" required>
                                <option value="gestion">Gestión</option>
                                <option value="acompanamiento">Acompañamiento</option>
                                <option value="entrega_documentos">Entrega de documentos</option>
                                <option value="reunion">Reunión</option>
                                <option value="otra">Otra</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="motivo">Motivo</label>
                            <textarea name="motivo" id="motivo" maxlength="255" class="form-control" required></textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="observaciones">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" maxlength="255" class="form-control"></textarea>
                        </div>

                    </div>

                    <button type="submit" class="au-btn au-btn-icon au-btn--green">
                        <i class="fa-solid fa-paper-plane"></i> Enviar solicitud
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/SGA-SEBANA/public/assets/js/afiliados.js"></script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/public/templates/base.html.php';
?>